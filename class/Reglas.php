<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Reglas extends BaseTable{

		protected $table = 'reglas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Devuelve las reglad para la ronda
		 * @param  int $ronda
		 * @return assoc_array
		 */
		public function getReglasByRonda($ronda){
			$where = 'ID_RONDA = ?';
			$values = array('ID_RONDA'=>$ronda);
			return $this->get($where, $values);
		}


		public function getRegla($id){
			return $this->find($id);
		}

	}

 ?>