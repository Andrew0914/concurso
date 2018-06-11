<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Categorias extends BaseTable{

		protected $table = 'categorias';

		public function __construct(){
			parent::__construct();
		}


		public function getCategorias(){
			$response = ['estado'=>0 , 'mensaje'=> 'No se obtuvieron las categorias'];
			try {
				$response['categorias'] = $this->get();
				$response['estado']=1;
				$response['mensaje']= 'Se obtuvieron las categorias';
			} catch (Exception $e) {
				$response['estado'] = 0;
				$response['mensaje'] = 'No se obtuvieron las categorias: '. $ex->getMessage();
			}
			return $response;
		}

		public function getCategoria($id){
			return $this->find($id);
		}
	}

	/**
	 * GET REQUEST
	 */
	if(isset($_GET['functionCategorias'])){
		$function = $_GET['functionCategorias'];
		$categoria = new Categorias();
		switch ($function) {
			case 'getCategorias':
				echo json_encode($categoria->getCategorias());
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida Categorias:GET']);
				break;
		}
	}
?>