<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Rondas extends BaseTable{

		protected $table = 'rondas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Devuelve la primer ronda para el tipo o etapa de concurso
		 * @param  [int] $etapa 
		 * @return 
		 */
		public function getPrimeraRonda($etapa){
			$whereClause = 'ID_ETAPA= ? ORDER BY ID_RONDA ASC';
			$values = ['ID_ETAPA' => $etapa];
			return $this->get($whereClause,$values)[0];
		}

		public function getRonda($id){
			return $this->find($id);
		}
	}
?>