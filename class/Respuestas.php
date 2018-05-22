<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Respuestas extends BaseTable{

		protected $table='respuestas';

		public function __construct(){
			parent::__construct();
		}

		public function getRespuestasByPregunta($pregunta){
			$whereClause = 'ID_PREGUNTA = :ID_PREGUNTA';
			$values = ['ID_PREGUNTA'=>$pregunta];
			return $this->get($whereClause, $values);
		}
	}
?>