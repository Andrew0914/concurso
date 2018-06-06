<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';


	class GradoDificultad extends BaseTable{

		protected $table= 'grados_dificultad';

		public function __construct(){
			parent::__construct();
		}

		public function getPuntaje($id){
			return $this->find($id)['PUNTAJE'];
		}
	}
 ?>