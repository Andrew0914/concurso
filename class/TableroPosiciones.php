<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__). '/Concursante.php';
	require_once dirname(__FILE__). '/Concursante.php';
	require_once dirname(__FILE__).'/util/Response.php';
	require_once dirname(__FILE__). '/CalculoPosiciones.php';
	
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

		public function guardaPosiciones($posiciones, $id_master){

			foreach ($posiciones as $posicion) {

				$empatado = 0;
				if( array_key_exists("empatado",$posicion) ){	
					if($posicion['empatado'] == 1){
						$empatado = 1;
					}
				}

				$posicion = [ 'ID_CONCURSANTE' => $posicion['ID_CONCURSANTE']
							, 'POSICION'=> $posicion['lugar']
							,'PUNTAJE_TOTAL'=>$posicion['totalPuntos']
							,'ID_TABLERO_MASTER' => $id_master
							,'EMPATADO' => $empatado  ];

				if(!$this->guardar($posicion)) return false;
			}

			return true;
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

		public function getPosicionPrevia($idConcursante , $idConcurso, $idMaster){
			$master = new TableroMaster();
			$id_master_previo = $master->getPrevio($idConcurso , $idMaster)['ID_TABLERO_MASTER'];
			$posicionPrevia = $this->get("ID_TABLERO_MASTER = ? AND ID_CONCURSANTE = ?", ['ID_TABLERO_MASTER' => $id_master_previo, 'ID_CONCURSANTE' => $idConcursante]);
			if(count($posicionPrevia) > 0 )
				return $posicionPrevia[0]['POSICION'];
			return null;
		}

		public function generaPosiciones($concurso, $es_empate){
			$calculo = new  CalculoPosiciones($this);
			return $calculo->generaPosiciones($concurso , $es_empate);
		}

		public function guardarNoEmpatados($idConcurso , $idMaster){
			$master = new TableroMaster();
			$id_master_previo = $master->getPrevio($idConcurso , $idMaster)['ID_TABLERO_MASTER'];
			$posiciones = $this->obtenerPosicionesTablero($id_master_previo);
			foreach($posiciones as $posicion){
				// GUARDA TODOS LOS LUGARAS GANADOS Y LOS MAYORES A LA POSICION 3
				if($posicion['EMPATADO'] == 0 ){
					unset($posicion['ID_TABLERO_POSICION']);
					$posicion['ID_TABLERO_MASTER'] = $idMaster;
					if(!$this->save($posicion)) return false;
				}
					
			}
			return true;
		}

		public function removerEmpateLugaresInferiores($id_master){
			return $this->update(0 , ['EMPATADO' => 0] , 'ID_TABLERO_MASTER =  ?  AND POSICION > 3 ', ['ID_TABLERO_MASTER' => $id_master] );
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