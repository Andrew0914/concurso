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

		public function getPreguntasByCategoriaGrado($categoria,$grado){
			$query = 'SELECT ID_PREGUNTA FROM preguntas WHERE ID_CATEGORIA = :ID_CATEGORIA AND ID_GRADO = :ID_GRADO';
			$values = [':ID_CATEGORIA'=>$categoria, ':ID_GRADO'=>$grado];

			return $this->query($query,$values);
		}
	}
 ?>