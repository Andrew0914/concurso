<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Categorias extends BaseTable{

		protected $table = 'categorias';

		public function __construct(){
			parent::__construct();
		}


		public function getCategorias(){
			return $this->get();
		}

		public function getCategoria($id){
			return $this->find($id);
		}
	}
?>