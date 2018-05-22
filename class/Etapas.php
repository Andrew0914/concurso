<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Etapas extends BaseTable{

		protected $table = 'etapas_tipo_concurso';

		public function __construct(){
			parent::__construct();
		}

		public function getEtapas(){
			return $this->get();
		}
		
	}

 ?>