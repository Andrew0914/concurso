<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(_FILE_).'/utils/Response.php';

	class Categorias extends BaseTable{

		protected $table = 'categorias';
		private $response = null;

		public function __construct(){
			parent::__construct();
			$this->response = new Response();
		}

		/**
		 * Devuelve todas las categorias
		 */
		public function getCategorias(){
			try {
				return $this->response->success('categorias' , 
					$this->get() , 
					'Se obtuvieron todas las categorias');
			} catch (Exception $ex) {
				return $this->response->fail('No se obtuvieron las categorias: '. $ex->getMessage());
			}
		}

		/**
		 * Devuelve las categorias permitidas para la ronda
		 * @param  Integer $etapa 
		 * @return array
		 */
		public function getCategoriasPermitidas($etapa){
			try {
				$sqlSatement = "SELECT c.* FROM categorias c INNER JOIN categorias_etapa ce ON c.ID_CATEGORIA = ce.ID_CATEGORIA WHERE ce.ID_ETAPA = ?";
				$valores = ['ID_ETAPA'=>$etapa];

				return $this->response->success('categorias' ,
					$this->query($sqlSatement,$valores),
					'Categorias obtenidas' );

			} catch (Exception $e) {
				return $this->response->fail($e->getMessage());
			}
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
			case 'getCategoriasPermitidas':
				echo json_encode($categoria->getCategoriasPermitidas($_GET['ID_ETAPA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida Categorias:GET']);
				break;
		}
	}
?>