<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Response.php';

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

		public function verCorrecta($idPregunta){
			$response = new Response();
			try{
				return $response->success( $this->get("ID_PREGUNTA = ? AND ES_CORRECTA = ?" , ["ID_PREGUNTA" => $idPregunta , "ES_CORRECTA" => 1])[0] , "Respuesta correcta");
			}catch(Exception $ex){
				return $response->fail('No se pudo obtener la respuesta correcta');
			}
		}

	}

	if( isset($_GET['functionRespuesta']) ){
		$respuesta = new Respuestas();
		$function = $_GET['functionRespuesta'];
		switch($function){
			case 'verCorrecta':
				echo json_encode($respuesta->verCorrecta($_GET['ID_PREGUNTA']));
			break;
		}
	}
?>