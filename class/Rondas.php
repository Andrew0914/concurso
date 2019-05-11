<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Response.php';

	class Rondas extends BaseTable{

		protected $table = 'rondas';

		public function __construct(){
			parent::__construct();
		}

	

		public function getRonda($id){
			return $this->find($id);
		}

		public function getRondas($etapa){
			$response = new Response();
			try{
				$where = "ID_ETAPA = ?";
				$values = ["ID_ETAPA"=> $etapa];
				return $response->success(['rondas' => $this->get($where , $values)],'se optuvieron las rondas');
			}catch(Exception $ex){
				return $response->fail('Rondas:'. $ex->getMessage());
			}
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

		public function getRondaDesempate($etapa){
			$where  = "ID_ETAPA=  ? AND IS_DESEMPATE = 1";
			$valores = ['ID_ETAPA'=>$etapa];
			return $this->get($where,$valores)[0];
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