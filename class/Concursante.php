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

		public function accederDesempate($idConcurso, $concursante){
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$ronda = new Rondas();
			$tabMaster = new TableroMaster();
			$lastMaster = $tabMaster->getLast($idConcurso);
			$tabPosiciones = new TableroPosiciones();
			$posiciones = $tabPosiciones->obtenerPosicionesActuales($lastMaster['ID_TABLERO_MASTER']);
			foreach ($posiciones as $p) {
				if($p['ID_CONCURSANTE'] == $concursante AND $p['EMPATADO'] == 1){
					return ['estado'=>1,
								'mensaje'=>'Has empatado con alguien',
								'ronda'=>$ronda->getRondaDesempate($concurso['ID_ETAPA'])];				}
			}
			return ['estado'=>0 , 'mensaje'=>'No has empadado con alguien, finalizo el concurso para ti'];
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
			// determinamos el empate
			$empate = 0;
			$info_empate = null;
			// solo si ya terminaron las rondas normales
			if($terminoRonda == 1){
				// Obtenemos la informacion de los tableros
				$tablero_master = new TableroMaster();
				$mMasters = $tablero_master->getTablerosMasters($idConcurso);
				$ultimoYaTienePosiciones = false;
				$ultimoNoCerrado = false;
				$tabPosiciones = new TableroPosiciones();
				$posiciones = null;
				
				if(count($mMasters) > 0) {
					$ultimoNoCerrado = $mMasters[count($mMasters) - 1]['CERRADO'] == 0;
					if($ultimoNoCerrado){
						return ['estado'=>'1' , 'termino_ronda' => $terminoRonda , 'calculo_empate'=>0 , 'mensaje'=>'El moderador aun no calcula los puntajes, por favor espera a que termine 1'];
					}
					$posiciones = $tabPosiciones->obtenerPosicionesActuales($mMasters[count($mMasters) - 1]['ID_TABLERO_MASTER']);
					if(count($posiciones) > 0){
						if($mMasters[count($mMasters) - 1]['POSICIONES_GENERADAS'] != 1){
							return ['estado'=>'1' , 'termino_ronda' => $terminoRonda , 
							'calculo_empate'=>0 , 'mensaje'=>'El moderador aun no calcula los puntajes, por favor espera a que termine 2'];
						}
					}

					$mMasters = $tablero_master->getTablerosMasters($idConcurso);
				}else{
					return ['estado'=>'1' , 'termino_ronda' => $terminoRonda , 'calculo_empate'=>0 , 'mensaje'=>'El moderador aun no calcula los puntajes, por favor espera a que termine 3'];
				}

				if(count($mMasters) > 0){
					$info_empate = $tabPosiciones->esEmpate($mMasters[count($mMasters) - 1]['ID_TABLERO_MASTER']);
					$empate = $info_empate['estado'];
					if($info_empate['estado'] == 1){
						// solo habilitamos los emptates para los primeros 3 lugares si es el caso
						for($x = 0 ; $x < count($info_empate['empatados']) ; $x++ ){
							if($info_empate['empatados'][$x]['POSICION'] > 3){
								unset($info_empate['empatados'][$x]);
							}
						}
					}
				}

				$cambio = ['estado'=>1,
					'yo_concursante' => $sesion->getOne(SessionKey::ID_CONCURSANTE),
					'mensaje'=>'Ha terminado la ronda y el calculo de puntajes',
					'termino_ronda'=>$terminoRonda,
					'calculo_empate'=>1,
					'empate'=>$empate,
					'info_empate'=>$info_empate];

				return $cambio;
			}

			return ['estado'=>1 , 'termino_ronda'=>$terminoRonda , 'mensaje'=>'Aun no termina la ronda, tu participacion ordinaria termino, pero es posible que aun puedas recibir preguntas de paso'];
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