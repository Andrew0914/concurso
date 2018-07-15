<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	
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
		public function generaPosiciones($mejores,$concurso){
			$valida = 1;
			$master = new TableroMaster();
			$id_master = $master->guardar(['ID_CONCURSO' => $concurso]);
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
		public function getTableros($concurso,$master_creado){
			$response = ['estado'=> 0 , 'mensaje' => 'No se pudieron obtener los tableros']; 
			$puntajes = new TableroPuntaje();
			$master = new TableroMaster();
			$mejores = $puntajes->getMejoresPuntajes($concurso)['mejores'];
			if($master_creado > 0){
				$response['master']= $master->getMaster($master_creado);
				$sentencia = "SELECT tp.*,c.CONCURSANTE FROM tablero_posiciones tp INNER JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE  WHERE tp.ID_TABLERO_MASTER = ? GROUP BY c.ID_CONCURSANTE ORDER BY tp.POSICION";
				$response['posiciones'] = $this->query($sentencia,['ID_TABLERO_MASTER'=>$master_creado]);
				$response['puntajes'] = $puntajes->getResultados($concurso);
				$response['estado'] = 1;
				$response['mensaje'] = 'Se obtuvieron los tableros exitosamente';
			}

			return $response;
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
				$puntaje = new TableroPuntaje();
				$concurso = $_POST['ID_CONCURSO'];
				$mejores = $puntaje->getMejoresPuntajes($concurso)['mejores'];
				echo json_encode($pos->generaPosiciones($mejores,$concurso));
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