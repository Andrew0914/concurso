<?php
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';

	class RondasLog extends BaseTable{

		protected $table = 'rondas_log';

		public function __construct(){
			parent::__construct();
		}

		public function guardar($log){
			return $this->save($log);
		}

		public function getRondasDisponibles($concurso,$rondaActual,$etapa){
			$query = "SELECT * FROM rondas WHERE ID_RONDA != ? AND ID_ETAPA= ? AND ID_RONDA NOT IN(SELECT ID_RONDA FROM rondas_log WHERE ID_CONCURSO = ?)";
			$values = ['ID_RONDA'=>$rondaActual , 'ID_ETAPA'=> $etapa , 'ID_CONCURSO'=>$concurso];
			$response = ['estado'=>0, 'mensaje'=> 'No se pudieron obtener las rondas'];
			try {
				$response['rondas'] = $this->query($query, $values, true);
				$response['estado']= 1;
				$response['mensaje']= 'Rondas obteneidas';
			} catch (Exception $e) {
				$response['estado']=0 ;
				$response['mensaje']= "RondasLog getRondasDisponibles:".$ex->getMessage();
			}

			return $response;
		}

		public function cambiarFinalizar($idConcurso,$rondaActual,$rondaNueva){
			$values = ['FIN'=>1];
			$whereClause = "ID_RONDA= ? AND ID_CONCURSO = ?";
			$whereValues = ['ID_RONDA'=> $rondaActual , 'ID_CONCURSO'=>$idConcurso];
			if($this->update(0, $values, $whereClause, $whereValues)){
				$log= ['ID_CONCURSO'=>$idConcurso, 'ID_RONDA'=>$rondaNueva];
				if($this->save($log)){
					$concurso = new Concurso();
					$values = ['ID_RONDA'=>$rondaNueva];
					if($concurso->actualiza($idConcurso,$values,null,null)){
						$sesion = new Sesion();
						$sesion->setOne(SessionKey::ID_RONDA , $rondaNueva);
						return ['estado'=>1, 'mensaje'=> 'Se finalizo la ronda anterior y se cambio a la ronda elegida'];
					}

					return ['estado'=>0 , 'mensaje'=>'No se genero la ronda'];
				}
				return ['estado'=>0 , 'mensaje'=>'No se pudo finalizar la ronda actual'];
			}
			return ['estado'=>0 , 'mensaje'=>'No se pudo cambiar de ronda'];
		}

		public function iniciarRonda($concurso,$ronda){
			$values = ['INICIO'=>1];
			$where = "ID_CONCURSO = ? AND ID_RONDA= ?";
			$whereValues = ['ID_CONCURSO'=> $concurso , 'ID_RONDA'=> $ronda];
			if($this->update(0, $values, $where, $whereValues))
				return ['estado'=> 1 , 'mensaje'=> 'Ronda iniciada con exito'];
			return ['estado'=> 0 , 'mensaje'=> 'No se pudo iniciar la ronda'];
		}

		public function finalizarRonda(){
			$values = ['FIN'=>1];
			$where = "ID_CONCURSO = ? AND ID_RONDA= ?";
			$whereValues = ['ID_CONCURSO'=> $concurso , 'ID_RONDA'=> $ronda];
			if($this->update(0, $values, $where, $whereValues))
				return ['estado'=> 1 , 'mensaje'=> 'Ronda finalizada con exito'];
			return ['estado'=> 0 , 'mensaje'=> 'No se pudo finalizar la ronda'];
		}

		public function isInProccessOrFinish($concurso,$ronda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND FIN = 1";
			$whereValues = ['ID_CONCURSO'=> $concurso , 'ID_RONDA'=> $ronda];
			$rs = $this->get($where,$whereValues);
			if(count($rs) > 0){
				return true;
			}
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND INICIO = 1";
			$rs = $this->get($where,$whereValues);
			if(count($rs) > 0){
				$query = "SELECT * FROM preguntas_generadas WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND HECHA = 1";
				$rs = $this->query($query,$whereValues);
				if(count($rs)> 0 ){
					return true;
				}
			}
			return false;
		}

		/**
		 * Devuelve true si ya termino la ronda de la categoria
		 * @param  [type] $concurso  [description]
		 * @param  [type] $categoria [description]
		 * @return [type]            [description]
		 */
		public function rondaCategoriaFinish($concurso,$categoria){
			$sentencia = "SELECT l.* FROM rondas_log l INNER JOIN rondas r ON l.ID_RONDA = r.ID_RONDA 
							WHERE r.IS_DESEMPATE = 0 AND l.ID_CONCURSO = ? AND ID_CATEGORIA = ? AND FIN = 1";
			$valores = ['ID_CONCURSO'=>$concurso, 'ID_CATEGORIA' => $categoria];
			return count($this->query($sentencia, $valores)) == 2 ;
		}

		public function eliminar($id,$where,$values){
			return $this->delete($id, $where, $values);
		}
	}
	/**
	 * POST REQUESTS
	 */

	if(isset($_POST['functionRondasLog'])){
		$function = $_POST['functionRondasLog'];
		$log = new RondasLog();
		switch ($function) {
			case 'cambiarFinalizar':
				echo json_encode($log->cambiarFinalizar($_POST['ID_CONCURSO'],$_POST['RONDA_ACTUAL'] , $_POST['RONDA_NUEVA']));
				break;
			default:
				echo  json_encode(['estado'=>0,'Operacion no valida RondasLog:POST']);
			break;
		}
	}
?>