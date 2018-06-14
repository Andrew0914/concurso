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

		public function getPuntajeDificultad($pregunta){
			$sentencia = "SELECT g.PUNTAJE FROM grados_dificultad g INNER JOIN preguntas p ON g.ID_GRADO = p.ID_GRADO WHERE ID_PREGUNTA = ?";
			$valores= ['ID_PREGUNTA'=>$pregunta];
			return $this->query($sentencia,$valores)[0]['PUNTAJE'];
		}
	}
 ?>