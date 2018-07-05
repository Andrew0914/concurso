<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Respuestas extends BaseTable{

		protected $table='respuestas';

		public function __construct(){
			parent::__construct();
		}

		public function getRespuestasByPregunta($pregunta){
			$whereClause = 'ID_PREGUNTA = ?';
			$values = ['ID_PREGUNTA'=>$pregunta];
			return $this->get($whereClause, $values);
		}

		public function esCorrecta($pregunta , $respuesta){
			if($respuesta <= 0 || $respuesta == '' || $respuesta == null){
				return 0;
			}
			$whereClause = "ID_PREGUNTA = ? AND ID_RESPUESTA= ?";
			$whereValues= ["ID_PREGUNTA" => $pregunta , "ID_RESPUESTA" => $respuesta];
			return $this->get($whereClause , $whereValues)[0]['ES_CORRECTA'];
		}
	}
?>