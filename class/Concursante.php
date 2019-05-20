<?php 
	
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	require_once dirname(__FILE__) . '/util/Response.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/RondasLog.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';

	class Concursante extends BaseTable{

		protected $table= 'concursantes';
		private $response;
		
		public function __construct(){
			parent::__construct();
			$this->response = new Response();
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
		 * Devuelve un concursante si existe para el concurso dado
		 * @param int $idConcurso
		 * @param int $idConcursante
		 * @param string $password
		 */
		private function existeConcursanteEnConcurso($idConcurso,$idConcursante,$password){
			$whereClause = ' ID_CONCURSO = ?  AND CONCURSANTE= ? AND PASSWORD= ?';
			$values = ['ID_CONCURSO'=>$idConcurso,'CONCURSANTE'=>$idConcursante,'PASSWORD'=>$password];
			$objConcursante = $this->get($whereClause,$values);
			return count($objConcursante) <= 0 ? null : $objConcursante;
		}

		/**
		 * Almacena los datos para la sesion
		 * @param object $concurso
		 * @param object $concursante
		 */
		private function establecerSesion($concurso , $concursante){
			$sesion = new Sesion();
			$valuesSesion = [SessionKey::ID_CONCURSANTE => $concursante[0]['ID_CONCURSANTE'] ,
							SessionKey::CONCURSANTE => $concursante[0]['CONCURSANTE'],
							SessionKey::ID_CONCURSO => $concursante[0]['ID_CONCURSO'],
							SessionKey::CONCURSANTE_POSICION => $concursante[0]['CONCURSANTE_POSICION'],
							SessionKey::ID_RONDA => $concurso['ID_RONDA'],
							SessionKey::ID_CATEGORIA => $concurso['ID_CATEGORIA']];

			$sesion->setMany($valuesSesion);
			return $valuesSesion;
		}
		
		/**
		 * Accede y general a sesion del concursante al concurso
		 * @param  [int] $concurso    
		 * @param  [string] $concursante 
		 * @param  [string] $password    
		 * @return [string json]              
		 */
		public function accederConcurso($idConcurso,$idConcursante,$password){
			$concursante = $this->existeConcursanteEnConcurso($idConcurso,$idConcursante,$password);
			if(!$concursante){ return $this->response->fail('Datos de concursante incorrectoss'); }

			$objConcurso = new Concurso();
			$concurso = $objConcurso->getConcurso($idConcurso);
			$preguntasGeneradas = new PreguntasGeneradas();

			if($preguntasGeneradas->inicioLanzamiento($concurso['ID_RONDA'],$concurso['ID_CONCURSO']
										,$concurso['NIVEL_EMPATE'])){
				return $this->response->fail('No es posible que entres a este concurso, el moderador ya ha comenzado a lanzar preguntas');
			}

			// verificamos que no hayan iniciado sesion con el mismo concursabte
			if($concursante[0]['INICIO_SESION'] == 1){
				return $this->response->fail('Ya han ingresado con este concursante');
			}

			// establecemos la sesion iniciada para este concursante
			$this->update($concursante[0]['ID_CONCURSANTE'] , ['INICIO_SESION'=>1]);
			$valoreSesion = $this->establecerSesion($concurso , $concursante);
			return $this->response->success(['concursante' => $valoreSesion], 'Inicio exitoso');
		}

		public function getConcursantes($concurso){
			try{
				$whereClause = "ID_CONCURSO=?";
				$whereValues = ['ID_CONCURSO'=>$concurso];
				return $this->response->success(['concursantes' => $this->get($whereClause,$whereValues)] , 'Concursantes obtenidos');
			}catch(Exception $ex){
				return $this->response->fail($ex->getMessage());
			}
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

		/**
		 * Devuelve el concursante que se encuetre en la siguiente posicion al concursante dado si es el ultimo regresa al primero
		 * @param integer $concursanteActual
		 * @param integer $concurso
		 */
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
		public function accederDesempate($idConcurso, $idConcursante){
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$tabMaster = new TableroMaster();

			// debe existir tableros calculados
			if(count($tabMaster->getTablerosMasters($idConcurso)) <= 0){
				return $this->response->fail('Aun no se genera ninguna tablero para determinar las puntuaciones,por favor espera a que el moderador lo genere');
			}

			// el ultimo tablero no debe estar cerrado
			$ultimoTableroMaster = $tabMaster->getLast($idConcurso);
			if($ultimoTableroMaster['CERRADO'] != 0){
				return ($concurso['FECHA_CIERRE'] != null AND $concurso['FECHA_CIERRE'] != '')
					? $this->response->success(['empatado' => 0] , 'El concurso ha sido cerrado') 
					: $this->response->fail('Aun no se determinan los puntajes por favor espera a que el moderador lo indique');
			}

			// el ultimo tablero ya debe tener todos los calculos hechos
			if($ultimoTableroMaster['POSICIONES_GENERADAS'] != 1){
				return $this->response->fail('Falta por calcularse las posicioens y determina posibles empates por favor espera a que el moderador lo indique');
			}

			// si ya fue validado
			$tabPosiciones = new TableroPosiciones();
			$posicionesActuales = $tabPosiciones->obtenerPosicionesActuales($ultimoTableroMaster['ID_TABLERO_MASTER']);
			$es_emaptado = $tabPosiciones->empateEnPosicion($idConcursante , $posicionesActuales);

			if($es_emaptado == 1){
				// si es que esto empatado la ronda de empate tiene que ser inicializada para que entre
				$ronda = new Rondas();
				if(!$ronda->getRonda($concurso['ID_RONDA'])['IS_DESEMPATE']){
					return $this->response->fail('Por favor espera a que el moderador pase al ronda de desempate para continuar,se han calculado empatados');
				}

				$log = new RondasLog();
				if(!$log->inicioRonda($concurso)){
					return $this->response->fail('Por favor espera a que el moderador pase al ronda de desempate para continuar,se han calculado empatados');
				}

				$rondaDesempate = $ronda->getRondaDesempate($concurso['ID_ETAPA']);
				return $this->response->success(['posiciones'=>$posicionesActuales , 'empatado'=>$es_emaptado , 'ronda'=>$rondaDesempate['ID_RONDA']] , 'Estas empatado');
			}

			return $this->response->success([ 'empatado'=>$es_emaptado],'No ha ocurrido empate');
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
		
		public function actualiza($id,$valores,$where,$whereValues){
			return $this->update($id,$valores,$where,$whereValues);
		}

		public function entraronTodos($idConcurso){
			$objConcursante = new Concursante();
			$concursantes = $objConcursante->getConcursantes($idConcurso)['concursantes'];
			foreach ($concursantes as $concursante){
				if($concursante['INICIO_SESION'] == 0){
					return false;
				}
			}
			return true;
		}

		public function liberarConcursante($idConcursante){
			try{
				$concursante = $this->find($idConcursante);
				if($concursante['INICIO_SESION'] == 0){
					return $this->response->fail('Este concursante puede iniciar sesion');
				}
				if($this->update($idConcursante , ['INICIO_SESION' => 0 ])){
					return $this->response->success([] , 'Concursante ' . $concursante['CONCURSANTE']. ' liberado para que pueda entrar');
				}
			}catch(Exception $ex){
				return $this->fail($ex->getMessage());
			}
		}
	}

	// POST REQUEST
	if(isset( $_POST['functionConcursante']) ){
		$function = $_POST['functionConcursante'];
		$concursante = new Concursante();
		switch ($function) {
			case 'accederConcurso':
				echo json_encode($concursante->accederConcurso($_POST['ID_CONCURSO'],$_POST['CONCURSANTE'],$_POST['PASSWORD']));
				break;
			case 'liberarConcursante':
				echo json_encode($concursante->liberarConcursante($_POST['ID_CONCURSANTE']));
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