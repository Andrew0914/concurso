<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__). '/Concursante.php';
	require_once dirname(__FILE__). '/Concursante.php';
	require_once dirname(__FILE__).'/util/Response.php';
	
	class TableroPosiciones extends BaseTable{

		protected $table='tablero_posiciones';
		private $response;

		public function __construct(){
			parent::__construct();
			$this->response = new Response();
		}

		/**
		 * Guarda las posiciones de los concursantes validando el empate
		 * @param  array $datos 
		 * @return         
		 */
		public function guardar($datos){
			return $this->save($datos);
		}

		/**
		 * Obtiene las posiciones del tablero master
		 * @param  integer $tablero_master 
		 * @return 
		 */
		public function obtenerPosicionesTablero($tablero_master){
			$where = "ID_TABLERO_MASTER = ?";
			$valores = ['ID_TABLERO_MASTER' => $tablero_master];
			return $this->get($where,$valores);
		}
		/**
		 * Ordena los lugares por total de puntajes
		 * @param  object $a 
		 * @param  object $b 
		 * @return boolean    
		 */
		private function cmp($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
		}

		private function cmpPuntaje($a , $b){	
		    if ($a['PUNTAJE_TOTAL'] == $b['PUNTAJE_TOTAL']) {
		        return 0;
		    }
		    return ($a['PUNTAJE_TOTAL'] > $b['PUNTAJE_TOTAL']) ? -1 : 1;
		}

		private function getMejoresGenerales($concurso,$es_empate){
			$puntaje = new TableroPuntaje();
			$mejores = $puntaje->getMejoresPuntajes($concurso,$es_empate)['mejores'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);

			// si es grupal contamos las puntuacioens del paso para medir las posiciones
			$mejores = $this->agregarPuntajesRobaPuntos($concurso , $objConcurso['ID_ETAPA'] , $mejores);
			return $mejores;
		}

		private function agregarPuntajesRobaPuntos($concurso , $etapa , $mejores){
			if($etapa == 2){
				$pasos = new TableroPaso();
				$mejoresPaso = $pasos->getMejores($concurso)['mejores'];
				for($x = 0 ; $x<count($mejores) ; $x++) {
					foreach ($mejoresPaso as $mp) {
						if($mejores[$x]['ID_CONCURSANTE'] == $mp['ID_CONCURSANTE']){
							$mejores[$x]['totalPuntos'] = $mejores[$x]['totalPuntos']  + $mp['totalPuntos'];
						}
					}
				}
				// como se le agregaron puntajes de paso cambian los lugres asi que ordenamos
				usort($mejores,array($this,"cmp"));
				//ajustamos lugares
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
			}

			return $mejores;
		}

		private function guardaPosicionesGenerales($concurso, $es_empate , $id_master){
			$mejores = $this->getMejoresGenerales($concurso, $es_empate);

			// almacenamos las posiciones brutas
			foreach ($mejores as $mejor) {
				$posicion = [ 'ID_CONCURSANTE' => $mejor['ID_CONCURSANTE']
							, 'POSICION'=> $mejor['lugar']
							,'PUNTAJE_TOTAL'=>$mejor['totalPuntos']
							,'ID_TABLERO_MASTER' => $id_master
							,'EMPATADO' => 0 ];

				if(!$this->guardar($posicion)) return false;
			}
			return true;
		}

		private function calcularEmpates($id_master,$es_empate,$concurso){
			// calculamos los empates
			$posiciones = $this->obtenerPosicionesTablero($id_master);
			$posicionesAnteriores = array();
			if($es_empate){
				$master = new TableroMaster();
				$posicionesAnteriores = $this->obtenerPosicionesTablero($master->getPrevio($concurso , $id_master)['ID_TABLERO_MASTER']);
			}
				
			foreach ($posiciones as $p) {
				foreach ($posiciones as $ps) {

					
					if($p['ID_CONCURSANTE'] != $ps['ID_CONCURSANTE'] 
						AND $p['PUNTAJE_TOTAL'] == $ps['PUNTAJE_TOTAL']
						AND $p['POSICION'] <= 3){
						// si es desempate
						
						$lugar_a_dar = 0;
						if($es_empate){
							$pPasado = $this->filtrarConcursante($posicionesAnteriores, $p['ID_CONCURSANTE']);
							$psPasado = $this->filtrarConcursante($posicionesAnteriores, $ps['ID_CONCURSANTE']);
							// si no venian de estar empatados en la misma posicion previamente, no pueden empatar entre si y se salta esta iteracion
							if($pPasado != null AND $psPasado != null ){
								if( $pPasado['POSICION'] == $psPasado['POSICION']){
									$lugar_a_dar = $pPasado['POSICION'];
								}else{
									continue;
								}
							}
						}else{
							$lugar_a_dar = $p['POSICION'];
						}

						if(!($this->update($ps['ID_TABLERO_POSICION'],['EMPATADO'=>1]) 
							AND $this->update($p['ID_TABLERO_POSICION'] , ['EMPATADO'=>1]))) return $this->response->fail('No se calcularon los empates');

						// cambiamos posicion (solo vivusal para las medallas)
						if(!$this->cambioMiposicion($ps['ID_CONCURSANTE'],$ps['ID_TABLERO_MASTER'])){
							$this->update($ps['ID_TABLERO_POSICION'] , ['POSICION'=>$lugar_a_dar , 'POSICION_CAMBIO'=>1]);
							$this->update($p['ID_TABLERO_POSICION'] , ['POSICION_CAMBIO'=>1]);
						}

					}
					
				}
			}
		}

		private function filtrarConcursante($posicionesAnteriores , $id_concursante){
			foreach($posicionesAnteriores as $posicion){
				if($posicion['ID_CONCURSANTE'] == $id_concursante)
					return $posicion;
			}

			return null;
		}

		/**
		 * Genera y almacena las posiciones de los puntajes
		 * @param  integer $concurso 
		 * @param  boolean $es_empate 
		 * @return boolean           
		 */
		public function generaPosiciones($concurso,$es_empate = false){
			$master = new TableroMaster();
			$id_master = $master->guardar(['ID_CONCURSO' => $concurso]);

			if($id_master <= 0) return $this->response->fail('No se pudo generar el tablero maestro');

			if(!$this->guardaPosicionesGenerales($concurso , $es_empate , $id_master))
				return $this->fail('No se alamacenaron las posiciones correctamente');

		
			$this->calcularEmpates($id_master,$es_empate,$concurso);

			$posicionesAnteriores = array();
			if($es_empate){
				$posicionesAnteriores = $this->obtenerPosicionesTablero($master->getPrevio($concurso , $id_master)['ID_TABLERO_MASTER']);
				$no_empatatados_previos = $this->filtrarEmpatados($posicionesAnteriores , 0);
				$empatatados_previos = $this->filtrarEmpatados($posicionesAnteriores , 1);

				foreach($no_empatatados_previos as $posicion){
					$posicion = [ 'ID_CONCURSANTE' => $posicion['ID_CONCURSANTE']
							, 'POSICION'=> $posicion['POSICION']
							,'PUNTAJE_TOTAL'=>$posicion['PUNTAJE_TOTAL']
							,'ID_TABLERO_MASTER' => $id_master
							,'EMPATADO' => 0 ];

				if(!$this->guardar($posicion)) return $this->response->fail('No se almacenaron los lugares previos en el nuevo tablero');
				}

				$posicionesActuales = $this->filtrarEmpatados($this->obtenerPosicionesTablero($id_master) , 0);
				$posicionesReasignadas = array_merge( $this->reasignacionPosicion($posicionesActuales ,$empatatados_previos,  1),			
													$this->reasignacionPosicion($posicionesActuales ,$empatatados_previos,  2),
													$this->reasignacionPosicion($posicionesActuales ,$empatatados_previos,  3));

				foreach($posicionesReasignadas as $posicioAsignada){
					if(!$this->update($posicioAsignada['ID_TABLERO_POSICION'], 
										['POSICION' => $posicioAsignada['POSICION'] ] ) ){
							
							return $this->response->fail('No se pudo establecer la reasignacion de posiciones');
					}
						
				}

			}

			if( !$master->actualiza($id_master ,['POSICIONES_GENERADAS' => 1]) ) return $this->response->fail('No se pudo establecer la bandera de posiciones generadas');

			return $this->response->success(['tablero_master'=>$id_master] , 'Tableros generados');
		}

		private function filtrarEmpatados($posiciones , $empatado){
			$filtro = array();

			foreach($posiciones as $posicion){
				if($posicion['EMPATADO'] == $empatado)
					$filtro[] = $posicion;
			}

			return $filtro;
		}

		private function reasignacionPosicion($posicionesActuales ,$empatatados_previos,  $lugar){
			$concursantesPosicion = array();
			foreach($posicionesActuales as $posicion){
				foreach($this->empatadosEnPosicion($empatatados_previos , $lugar ) as $empatado){
					if($posicion['ID_CONCURSANTE']  == $empatado['ID_CONCURSANTE']){
						$concursantesPosicion[] = $posicion;
					}
				}
			}

			usort($concursantesPosicion,array($this,"cmpPuntaje"));

			for($i= 0 ; $i< count($concursantesPosicion) ; $i++){
				$concursantesPosicion[$i]['POSICION'] = $lugar++;  
			}

			return $concursantesPosicion;
		}

		private function empatadosEnPosicion($empatados , $lugar){
			$filtro = array();

			foreach($empatados as $posicion){
				if($posicion['POSICION'] == $lugar)
					$filtro[] = $posicion;
			}

			return $filtro;
		}

		public function cambioMiposicion($concursante, $master){
			$where = "ID_CONCURSANTE = ? AND ID_TABLERO_MASTER = ?";
			$valores = ['ID_CONCURSANTE'=>$concursante , 'ID_TABLERO_MASTER' => $master];
			return $this->get($where, $valores)[0]['POSICION_CAMBIO'];
		}

		/**
		 * Obtiene los tableros actuales del concurso
		 * @param  integer $concurso      
		 * @param  integer $master_creado 
		 * @param boolean $es_empate
		 * @return array                
		 */
		public function getTableros($concurso,$master_creado,$es_empate){
			$puntajes = new TableroPuntaje();
			$master = new TableroMaster();
			$pasos = new TableroPaso();
			if($master_creado > 0){
				$sentencia = "SELECT tp.*,c.CONCURSANTE FROM tablero_posiciones tp INNER JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE  
							WHERE tp.ID_TABLERO_MASTER = ? GROUP BY c.ID_CONCURSANTE ORDER BY tp.POSICION";
				return $this->response->success([
						'master' => $master->getMaster($master_creado),
						'posiciones' => $this->query($sentencia,['ID_TABLERO_MASTER'=>$master_creado]),
						'puntajes' => $puntajes->getResultados($concurso,$es_empate,false),
						'pasos' => $pasos->getResultados($concurso)
					],'Se obtuvieron los tableros exitosamente');
			}
			return $this->response->fail('No se pudieron obtener los tableros');
		}

		public function esEmpate($master){
			$posiciones = $this->get("ID_TABLERO_MASTER = ?",['ID_TABLERO_MASTER' => $master]);
			$emptatados = 0;
			$concursante = new Concursante();
			for($i = 0; $i < count($posiciones) ; $i++) {
				if($posiciones[$i]['EMPATADO'] == 1){
					$emptatados++;
				}
				$posiciones[$i]['CONCURSANTE'] = $concursante->getConcursante($posiciones[$i]['ID_CONCURSANTE'])['CONCURSANTE'];
			}

			if($emptatados > 0){
				return $this->response->success(['empatados' => $posiciones ] , 'Se generó empate');
			}else{
				return ['estado' => 2 , 'mensaje'=>'No se generó empate' , 'empatados' => $posiciones];
			}
			return $this->response->fail('No se generó ningún empate');
		}

		public function esEmpateByConcurso($idConcurso){
			$master = new TableroMaster();
			$last = $master->getLast($idConcurso);
			return $this->esEmpate($last['ID_TABLERO_MASTER']);
		}

		public function getByMasters($masters){
			$tableros = [];
			foreach ($masters as $m) {
				$tableros["tableros"][] = $this->get("ID_TABLERO_MASTER = ?",['ID_TABLERO_MASTER'=>$m['ID_TABLERO_MASTER']]);
			}
			return $tableros;
		}

		/**
		 * Devuelve la cantidad e concursantes empatados en el tablero master indicado
		 * @param  integer $last_master 
		 * @return integer          
		 */
		public function getCountEmpatados($last_master){
			$sentencia = "SELECT COUNT(*) as TOTAL FROM tablero_posiciones WHERE ID_TABLERO_MASTER = ? AND EMPATADO = 1";
			$valores = ['ID_TABLERO_MASTER' => $last_master];
			return $this->query($sentencia,$valores)[0]['TOTAL'];
		}

		public function eliminar($id=0,$where="",$whereValues = []){
			return $this->delete($id,$where,$whereValues);
		}

		/**
		 * Verifica si el concursante esta empatado en las posiciones dadas
		 * @param int $idConcursante
		 * @param array $posiciones
		 */
		public function empateEnPosicion($idConcursante , $posiciones){
			$es_emaptado = 0;
			foreach ($posiciones as $posicion) {
				if($posicion['ID_CONCURSANTE'] == $idConcursante){
					if($posicion['EMPATADO'] == 1){
						$es_emaptado = 1;
						break;
					}
				}
			}

			return $es_emaptado;
		}

	}	

	/**
	 * POST REQUESTS
	 */
	if(isset($_POST['functionTabPosiciones'])){
		$function = $_POST['functionTabPosiciones'];
		$tablero = new TableroPosiciones();
		switch ($function) {
			case 'generaPosicionesx':
				$concurso = $_POST['ID_CONCURSO'];
				$es_desempate = $_POST['IS_DESEMPATE'];
				echo json_encode($tablero->generaPosiciones($concurso,$es_desempate));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:POST']);
				break;
		}
	}

?>