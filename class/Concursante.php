<?php 
	
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	
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
			$whereClause = ' ID_CONCURSO = :ID_CONCURSO  AND CONCURSANTE= :CONCURSANTE AND PASSWORD= :PASSWORD';
			$values = ['ID_CONCURSO'=>$concurso,'CONCURSANTE'=>$concursante,'PASSWORD'=>$password];
			$objConcursante = $this->get($whereClause,$values);
			if(count($objConcursante) <= 0){
				return json_encode(['estado'=>0, 'mensaje'=> 'No eres un concursante de este concurso']);
			}
			$sesion = new Sesion();

			$valuesSesion = [SessionKey::ID_CONCURSANTE => $objConcursante[0]['ID_CONCURSANTE'] ,
							SessionKey::CONCURSANTE => $objConcursante[0]['CONCURSANTE'],
							SessionKey::ID_CONCURSO => $objConcursante[0]['ID_CONCURSO'],
							SessionKey::CONCURSANTE_POSICION => $objConcursante[0]['CONCURSANTE_POSICION']];

			$sesion->setMany($valuesSesion);
			
			return json_encode(['estado'=>1, 'mensaje'=> 'Inicio exitoso','concursante'=>$valuesSesion]); 
		}

		public function getConcursantes($concurso){
			$response =[ 'estado'=>0, 'mensaje'=>'No se realizo la operacion'];
			try{
				$whereClause = "ID_CONCURSO=:ID_CONCURSO";
				$whereValues = ['ID_CONCURSO'=>$concurso];
				$response['concursantes'] = $this->get($whereClause,$whereValues);
				$response['estado']= 1;
				$response['mensaje']= "Concursantes obtenidos";
			}catch(Exception $ex){
				$response['mensaje'] = $ex->getMessage();
			}

			return $response;
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
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSANTE']);
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
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSANTE']);
			break;
		}
	}

 ?>