<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	
	class TableroMaster extends BaseTable{

		protected $table='tablero_master';

		public function __construct(){
			parent::__construct();
		}

		public function guardar($datos){
			return $this->save($datos);
		}

		public function getMaster($id){
			return $this->find($id);
		}
		
	}	
?>