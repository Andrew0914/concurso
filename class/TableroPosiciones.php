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
		 * @return array_assoc
		 */
		public function obtenerPosicionesActuales($tablero_master){
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

		private function getMejoresOnTableroMaster($concurso,$es_empate){
			$puntaje = new TableroPuntaje();
			$mejores = $puntaje->getMejoresPuntajes($concurso,$es_empate)['mejores'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);

			// si es grupal contamos las puntuacioens del paso para medir las posiciones
			if($objConcurso['ID_ETAPA'] == 2){
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

		/**
		 * Genera y almacena las posiciones de los puntajes
		 * @param  array $mejores  
		 * @param  integer $concurso 
		 * @return boolean           
		 */
		public function generaPosiciones($concurso,$es_empate = false){
			$master = new TableroMaster();
			$id_master = $master->guardar(['ID_CONCURSO' => $concurso]);

			if($id_master <= 0) return $this->response->fail('No se pudo generar el tablero maestro');

			$mejores = $this->getMejoresOnTableroMaster($concurso, $es_empate);

			// almacenamos las posiciones brutas
			foreach ($mejores as $mejor) {
				$posicion = [ 'ID_CONCURSANTE' => $mejor['ID_CONCURSANTE']
							, 'POSICION'=> $mejor['lugar']
							,'PUNTAJE_TOTAL'=>$mejor['totalPuntos']
							,'ID_TABLERO_MASTER' => $id_master
							,'EMPATADO' => 0 ];

				if(!$this->guardar($posicion)) return $this->fail('No se alamacenaron las posiciones correctamente');
			}

			// calculamos los empates
			$posiciones = $this->obtenerPosicionesActuales($id_master);
			foreach ($posiciones as $p) {
				foreach ($posiciones as $ps) {
					if($p['ID_CONCURSANTE'] != $ps['ID_CONCURSANTE'] 
						AND $p['PUNTAJE_TOTAL'] == $ps['PUNTAJE_TOTAL']
						AND $p['POSICION'] <= 3){
						if(!($this->update($ps['ID_TABLERO_POSICION'],['EMPATADO'=>1]) 
						AND $this->update($p['ID_TABLERO_POSICION'] , ['EMPATADO'=>1]))) return $this->response->fail('No se calcularon los empates');

						// cambiamos posicion (solo vivusal para las medallas)
						if(!$this->cambioMiposicion($ps['ID_CONCURSANTE'],$ps['ID_TABLERO_MASTER'])){
							$this->update($ps['ID_TABLERO_POSICION'] , ['POSICION'=>$p['POSICION'] , 'POSICION_CAMBIO'=>1]);
							$this->update($p['ID_TABLERO_POSICION'] , ['POSICION_CAMBIO'=>1]);
						}

					}
				}
			}

			if( !$master->actualiza($id_master ,['POSICIONES_GENERADAS' => 1]) ) return $this->response->fail('No se pudo establecer la bandera de posiciones generadas');

			return $this->response->success(['tablero_master'=>$id_master] , 'Tableros generados');
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
				$sentencia = "SELECT tp.*,c.CONCURSANTE FROM tablero_posiciones tp INNER JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE  WHERE tp.ID_TABLERO_MASTER = ? GROUP BY c.ID_CONCURSANTE ORDER BY tp.POSICION";
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
				return $this->response->success(['empatados' => $posiciones ] , 'Se genero empate');
			}else{
				return ['estado' => 2 , 'mensaje'=>'No se genero empate' , 'empatados' => $posiciones];
			}
			return $this->response->fail('No se genero ningun empate');
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