<?php 
	
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/RondasLog.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';

	class Concursante extends BaseTable{

		protected $table= 'concursantes';
		
		public function __construct(){
			parent::__construct();
		}

		/**
		 * Almacena un concursante
		 * @param  [assoc_array] $concursante 
		 * @return [int]              
		 */
		public function saveConcursantes($concursante){
			return $this->save($concursante);
		}
		
		/**
		 * Accede y general a sesion del concursante al concurso
		 * @param  [int] $concurso    
		 * @param  [string] $concursante 
		 * @param  [string] $password    
		 * @return [string json]              
		 */
		public function accederConcurso($concurso,$concursante,$password){
			$whereClause = ' ID_CONCURSO = ?  AND CONCURSANTE= ? AND PASSWORD= ?';
			$values = ['ID_CONCURSO'=>$concurso,'CONCURSANTE'=>$concursante,'PASSWORD'=>$password];
			$objConcursante = $this->get($whereClause,$values);
			if(count($objConcursante) <= 0){
				return json_encode(['estado'=>0, 'mensaje'=> 'No eres un concursante de este concurso']);
			}
			$objConcurso = new Concurso();
			$aConcurso = $objConcurso->getConcurso($concurso);
			$gen = new PreguntasGeneradas();
			if($gen->inicioLanzamiento($aConcurso['ID_RONDA'],$aConcurso['ID_CONCURSO'],$aConcurso['NIVEL_EMPATE'])){
				return json_encode(['estado'=>0
					, 'mensaje'=> 'No es posible que entres a este concurso, el moderador ya ha comenzado a lanzar preguntas']);
			}
			$sesion = new Sesion();
			$valuesSesion = [SessionKey::ID_CONCURSANTE => $objConcursante[0]['ID_CONCURSANTE'] ,
							SessionKey::CONCURSANTE => $objConcursante[0]['CONCURSANTE'],
							SessionKey::ID_CONCURSO => $objConcursante[0]['ID_CONCURSO'],
							SessionKey::CONCURSANTE_POSICION => $objConcursante[0]['CONCURSANTE_POSICION'],
							SessionKey::ID_RONDA => $aConcurso['ID_RONDA'],
							SessionKey::ID_CATEGORIA => $aConcurso['ID_CATEGORIA']];

			$sesion->setMany($valuesSesion);

			
			return json_encode(['estado'=>1, 
				'mensaje'=> 'Inicio exitoso',
				'concursante'=>$valuesSesion]); 
		}

		public function getConcursantes($concurso){
			$response =[ 'estado'=>0, 'mensaje'=>'No se realizo la operacion'];
			try{
				$whereClause = "ID_CONCURSO=?";
				$whereValues = ['ID_CONCURSO'=>$concurso];
				$response['concursantes'] = $this->get($whereClause,$whereValues);
				$response['estado']= 1;
				$response['mensaje']= "Concursantes obtenidos";
			}catch(Exception $ex){
				$response['mensaje'] = $ex->getMessage();
			}

			return $response;
		}

		public function eliminar($id,$where,$values){
			return $this->delete($id, $where, $values);
		}

		public function getCountConcursates($concurso){
			$sentencia = "SELECT COUNT(ID_CONCURSANTE) as total FROM concursantes WHERE ID_CONCURSO = ?";
			$valores = ['ID_CONCURSO' => $concurso];
			return $this->query($sentencia, $valores, true);
		}

		public function getFirst($concurso){
			$sentencia = "SELECT * FROM concursantes WHERE ID_CONCURSO = ? ORDER BY CONCURSANTE_POSICION ASC LIMIT 1";
			$valores = [$concurso];
			return $this->query($sentencia,$valores)[0];
		}

		public function getLast($concurso){
			$sentencia = "SELECT * FROM concursantes WHERE ID_CONCURSO = ? ORDER BY CONCURSANTE_POSICION DESC LIMIT 1";
			$valores = [$concurso];
			return $this->query($sentencia,$valores)[0];
		}

		public function getConcursanteByPosicion($concurso,$posicion){
			$where = "ID_CONCURSO = ? AND CONCURSANTE_POSICION = ? ";
			$valores = ['ID_CONCURSO'=>$concurso,"CONCURSANTE_POSICION"=>$posicion];
			return $this->get($where,$valores)[0];
		}

		public function getConcursante($id){
			return $this->find($id);
		}

		public function siguiente($concursanteActual,$concurso){
			$concursantes = $this->getConcursantes($concurso)['concursantes'];
			$actual = $this->find($concursanteActual);

			foreach ($concursantes as $c) {
				if($actual['CONCURSANTE_POSICION'] >= count($concursantes)){
					if($c['CONCURSANTE_POSICION'] == 1){
						return $c;
					}
				}
				if($c['CONCURSANTE_POSICION'] == ($actual['CONCURSANTE_POSICION'] + 1)){
					return $c; 
				}
			}
		}

		/**
		 * Determina si el concursante puede acceder al desempate o no 
		 * @param  integer $idConcurso  
		 * @param  integer $concursante 
		 * @return array              
		 */
		public function accederDesempate($idConcurso, $concursante){
			
			$tabMaster = new TableroMaster();
			// debe existir tableros calculados
			if(count($tabMaster->getTablerosMasters($idConcurso)) <= 0){
				return ['estado' => 0 
				, 'mensaje' => 'Aun no se genera ninguna tablero para determinar las puntuaciones,por favor espera a que el moderador lo genere']; 
			}
			// el ultimo tablero no debe estar cerrado
			$last = $tabMaster->getLast($idConcurso);
			if($last['CERRADO'] != 0){
				return ['estado' => 0 
				, 'mensaje' => 'Aun no se determinan los puntajes por favor espera a que el moderador lo indique']; 
			}
			// el ultimo tablero ya debe tener todos los calculos hechos
			if($last['POSICIONES_GENERADAS'] != 1){
				return ['estado' => 0 
				, 'mensaje' => 'Falta por calcularse las posicioens y determina posibles empates por favor espera a que el moderador lo indique']; 
			}
			// si ya fue validado
			$tabPosiciones = new TableroPosiciones();
			$mPosiciones = $tabPosiciones->obtenerPosicionesActuales($last['ID_TABLERO_MASTER']);
			$es_emaptado = 0;
			foreach ($mPosiciones as $pos) {
				if($pos['ID_CONCURSANTE'] == $concursante){
					if($pos['EMPATADO'] == 1){
						$es_emaptado = 1;
						break;
					}
				}
			}
			if($es_emaptado == 1){
				// si es que esto empatado la ronda de empata tiene que ser inicializada para que entre
				$concurso = new Concurso();
				$concurso = $concurso->getConcurso($idConcurso);
				$ronda = new Rondas();

				if(!$ronda->getRonda($concurso['ID_RONDA'])['IS_DESEMPATE']){
					return ['estado' => 0 
					, 'mensaje' => 'Por favor espera a que el moderador pase al ronda de desempate para continuar,se han calculado empatados']; 
				}
				$log = new RondasLog();
				if(!$log->inicioRonda($concurso)){
					return ['estado' => 0 
					, 'mensaje' => 'Por favor espera a que el moderador pase al ronda de desempate para continuar,se han calculado empatados']; 
				}
				$rondaDesempate = $ronda->getRondaDesempate($concurso['ID_ETAPA']);
				return ['estado' => 1 
					, 'mensaje'=> 'Estas empatdo' 
					, 'posiciones'=>$mPosiciones 
					, 'empatado'=>$es_emaptado 
					, 'ronda'=>$rondaDesempate['ID_RONDA']];
			}

			return ['estado'=> 1, 'mensaje' => 'No ha ocurrido empate', 'empatado'=>$es_emaptado];
		}

		/**
		 * Efectua el cambio de ronda de la 2nda ronda grupal al posible o no desempate
		 * @param  integer $idConcurso 
		 * @return array
		 */
		public function terminarParticipacionGrupal($idConcurso){
			$rondaLog = new RondasLog();
			$sesion = new Sesion();
			$logs = $rondaLog->getLogs($idConcurso);
			$terminoRonda = 0;
			foreach ($logs as $log) {
				if($log['ID_RONDA'] == 5 AND $log['FIN'] == 1){
					$terminoRonda = 1;
					break;
				}
			}
			return ['estado'=>1 , 'termino_ronda'=>$terminoRonda ];
		}
	}

	// POST REQUEST
	if(isset( $_POST['functionConcursante']) ){
		$function = $_POST['functionConcursante'];
		$concursante = new Concursante();
		switch ($function) {
			case 'accederConcurso':
				echo $concursante->accederConcurso($_POST['ID_CONCURSO'],$_POST['CONCURSANTE'],$_POST['PASSWORD']);
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSANTE:POST']);
			break;
		}
	}

	// GET REQUEST
	if(isset($_GET['functionConcursante'])){
		$function = $_GET['functionConcursante'];
		$concursante = new Concursante();
		switch ($function) {
			case 'getConcursantes':
				echo json_encode($concursante->getConcursantes($_GET['concurso']));
				break;
			case 'accederDesempate':
				echo json_encode($concursante->accederDesempate($_GET['ID_CONCURSO'],$_GET['ID_CONCURSANTE']));
				break;
			case 'terminarParticipacionGrupal':
				echo json_encode($concursante->terminarParticipacionGrupal($_GET['ID_CONCURSO']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSANTE:GET']);
			break;
		}
	}

 ?>