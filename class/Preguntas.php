<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Preguntas extends BaseTable{

		protected $table='preguntas';

		public function __construct(){
			parent::__construct();
		}

		public function findPregunta($id){
			return $this->find($id);
		}
	}
 ?>