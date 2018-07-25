<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__). '/Concursante.php';
	
	class TableroPosiciones extends BaseTable{

		protected $table='tablero_posiciones';

		public function __construct(){
			parent::__construct();
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
		 * Genera y almacena las posiciones de los puntajes
		 * @param  array $mejores  
		 * @param  integer $concurso 
		 * @return boolean           
		 */
		public function generaPosiciones($concurso,$es_empate = false){
			$valida = 1;
			$master = new TableroMaster();
			$id_master = $master->guardar(['ID_CONCURSO' => $concurso]);
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
			}
			if($id_master <= 0){
				return ['estado'=>0 , 'mensaje' => 'No se pudo generar el tablero maestro'];
			}
			// almacenamos las posiciones brutas
			foreach ($mejores as $mejor) {
				$posicion = [ 'ID_CONCURSANTE' => $mejor['ID_CONCURSANTE']
							, 'POSICION'=> $mejor['lugar']
							,'PUNTAJE_TOTAL'=>$mejor['totalPuntos']
							,'ID_TABLERO_MASTER' => $id_master
							,'EMPATADO' => 0 ];
				if(!$this->guardar($posicion)){
					$valida *= 0;
				}
			}

			if(!$valida){
				return ['estado'=>0 , 'mensaje'=> 'No se alamacenaron las posiciones correctamente'];
			}
			// calculamos los empates
			$valida = 1;
			$posiciones = $this->obtenerPosicionesActuales($id_master);
			foreach ($posiciones as $p) {
				foreach ($posiciones as $ps) {
					if($p['ID_CONCURSANTE'] != $ps['ID_CONCURSANTE'] 
						AND $p['PUNTAJE_TOTAL'] == $ps['PUNTAJE_TOTAL']
						AND $p['POSICION'] <= 3){
						if(!($this->update($ps['ID_TABLERO_POSICION'],['EMPATADO'=>1])
							AND $this->update($p['ID_TABLERO_POSICION'] , ['EMPATADO'=>1]))){
							$valida *= 0;
						}
						// cambiamos posicion (solo vivusal para las medallas)
						if(!$this->cambioMiposicion($ps['ID_CONCURSANTE'],$ps['ID_TABLERO_MASTER'])){
							$this->update($ps['ID_TABLERO_POSICION'] , ['POSICION'=>$p['POSICION'] , 'POSICION_CAMBIO'=>1]);
							$this->update($p['ID_TABLERO_POSICION'] , ['POSICION_CAMBIO'=>1]);
						}
					}
				}
			}

			if(!$valida){
				return ['estado'=>0 , 'mensaje'=> 'No se calcularon los empates'];
			}

			// por utlimo indicamos que ya las posiciones fueron genradas por temas del timer
			$master = new TableroMaster();
			
			if( !$master->actualiza($id_master ,['POSICIONES_GENERADAS' => 1]) ){
				return ['estado'=>'No se pudo establecer la bandera de posiciones generadas'];
			}

			return ['estado'=>1 , 'mensaje' => 'Tableros generados' , 'tablero_master'=>$id_master ];
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
		 * @return array                
		 */
		public function getTableros($concurso,$master_creado,$es_empate){
			$response = ['estado'=> 0 , 'mensaje' => 'No se pudieron obtener los tableros']; 
			$puntajes = new TableroPuntaje();
			$master = new TableroMaster();
			$pasos = new TableroPaso();
			$mejores = $puntajes->getMejoresPuntajes($concurso,$es_empate)['mejores'];
			if($master_creado > 0){
				$response['master']= $master->getMaster($master_creado);
				$sentencia = "SELECT tp.*,c.CONCURSANTE FROM tablero_posiciones tp INNER JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE  WHERE tp.ID_TABLERO_MASTER = ? GROUP BY c.ID_CONCURSANTE ORDER BY tp.POSICION";
				$response['posiciones'] = $this->query($sentencia,['ID_TABLERO_MASTER'=>$master_creado]);
				$response['puntajes'] = $puntajes->getResultados($concurso,$es_empate);
				$response['pasos'] = $pasos->getResultados($concurso);
				$response['estado'] = 1;
				$response['mensaje'] = 'Se obtuvieron los tableros exitosamente';
			}

			return $response;
		}

		public function esEmpate($master){
			$where = "ID_TABLERO_MASTER = ?";
			$valores = ['ID_TABLERO_MASTER' => $master];
			$response = ['estado'=>0, 'mensaje'=> 'No se determino el empate correctamente'];
			$rs = $this->get($where,$valores);
			$emptatados = 0;
			$concursante = new Concursante();
			for($i = 0; $i < count($rs) ; $i++) {
				if($rs[$i]['EMPATADO'] == 1){
					$emptatados++;
				}
				$rs[$i]['CONCURSANTE'] = $concursante->getConcursante($rs[$i]['ID_CONCURSANTE'])['CONCURSANTE'];
			}

			if($emptatados > 0){
				$response['estado'] = 1;
				$response['mensaje'] ="Se genero empate";
			}else{
				$response['estado'] = 2;
				$response['mensaje'] = 'No se genero ningun empate';
			}
			$response['empatados'] = $rs;
			return $response;
		}

		public function esEmpateByConcurso($idConcurso){
			$master = new TableroMaster();
			$last = $master->getLast($idConcurso);
			return $this->esEmpate($last['ID_TABLERO_MASTER']);
		}

		public function getByMasters($masters){
			$tableros = [];
			foreach ($master as $m) {
				$tableros["tableros"][] = $this->get("ID_TABLERO_MASTER = ?",['ID_TABLERO_MASTER'=>$m['ID_TABLERO_MASTER']]);
			}
			return $tableros;
		}
	}	

	/**
	 * POST REQUESTS
	 */
	if(isset($_POST['functionTabPosiciones'])){
		$function = $_POST['functionTabPosiciones'];
		$pos = new TableroPosiciones();
		switch ($function) {
			case 'generaPosiciones':
				$concurso = $_POST['ID_CONCURSO'];
				$es_desempate = $_POST['IS_DESEMPATE'];
				echo json_encode($pos->generaPosiciones($concurso,$es_desempate));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TableroPosiciones:POST']);
				break;
		}
	}

	/**
	 * GET REQUESTS 
	 */
	if(isset($_GET['functionTabPosiciones'])){
		$function = $_GET['functionTabPosiciones'];
		$pos = new TableroPosiciones();
		switch ($function) {
			case 'getTableros':
				echo  json_encode($pos->getTableros($_GET['ID_CONCURSO'] , $_GET['ID_TABLERO_MASTER']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TableroPosiciones:GET']);
				break;
		}	
	}
?>