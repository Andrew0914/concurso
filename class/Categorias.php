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

		/**
		 * Devuelve las categorias permitidas para la ronda
		 * @param  Integer $etapa 
		 * @return array
		 */
		public function getCategoriasPermitidas($etapa){
			$rs = ['estado'=>0 , 'mensaje'=>'No se obtuvieron las categorias'];
			try {
				$sentencia = "SELECT c.* FROM categorias c INNER JOIN categorias_etapa ce ON c.ID_CATEGORIA = ce.ID_CATEGORIA WHERE ce.ID_ETAPA = ?";
				$valores = ['ID_ETAPA'=>$etapa];
				$rs['categorias'] =  $this->query($sentencia,$valores);
				$rs['estado']=1;
				$rs['mensaje']= 'Categorias obtenidas exitosamente'; 
			} catch (Exception $e) {
				$rs = ['estado'=>0 , 'mensaje'=>$e->getMessage()];
			}
			
			return $rs;
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