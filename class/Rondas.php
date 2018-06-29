<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Rondas extends BaseTable{

		protected $table = 'rondas';

		public function __construct(){
			parent::__construct();
		}

	

		public function getRonda($id){
			return $this->find($id);
		}

		public function getRondas($etapa){
			$response = ['estado' => '0', 'mensaje'=>'No se obtivieron las rondas'];
			try{
				$where = "ID_ETAPA = ?";
				$values = ["ID_ETAPA"=> $etapa];
				$response['estado']=1;
				$response['mensaje']= 'se optuvieron las rondas';
				$response['rondas'] = $this->get($where , $values);
			}catch(Exception $ex){
				$response['estado']=0;
				$response['mensaje']= 'Rondas:'. $ex->getMessage();
			}
			return $response;
		}

		/**
		 * Devuelve la primer ronda para el tipo o etapa de concurso
		 * @param  integer $etapa 
		 * @return array ronda
		 */
		public function getPrimeraRonda($etapa){
			$whereClause = 'ID_ETAPA= ? ORDER BY ID_RONDA ASC';
			$values = ['ID_ETAPA' => $etapa];
			return $this->get($whereClause,$values)[0];
		}
	}

	/**
	 * GET REQUEST
	 */
	if(isset($_GET['functionRonda'])){
		$function = $_GET['functionRonda'];
		$ronda = new Rondas();
		switch ($function) {
			case 'getRondas':
				echo json_encode($ronda->getRondas($_GET['etapa']));
			break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida Rondas:GET']);
			break;
		}
	}
?>