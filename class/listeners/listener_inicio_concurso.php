<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	require_once dirname(__FILE__) . '/../PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/../Respuestas.php';
	require_once dirname(__FILE__) . '/../Rondas.php';

	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		// mientras el concurso no se inicie, se queda esperando y renueva la info del concurso para validar el inicio
		while ($concurso['INICIO_CONCURSO'] == 0 ) {
			usleep(5000);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		}
		// cuando ya inicio obtiene lasp reguntas generadas para el concurso para la primer ronda individual
		try{
			$preguntasGeneradas = new PreguntasGeneradas();
			$preguntas = $preguntasGeneradas->getPreguntasByConcursoRonda($concurso['ID_CONCURSO'],1);
			$respeusta = new Respuestas();
			// agregamos las respuestas para cada pregunta
			for ($cont = 0 ; $cont < count($preguntas); $cont++) {
				$preguntas[$cont]['respuestas'] = $respeusta->getRespuestasByPregunta($preguntas[$cont]['ID_PREGUNTA']);
			}
			// devolvemos la informacion de la ronda tambien
			$ronda = new Rondas();
			$ronda = $ronda->getRonda(1);
			echo json_encode(array('estado'=>1,
				'mensaje'=>'Preguntas generadas para la ronda.',
				'preguntas'=>$preguntas,
				'ronda'=>$ronda));
		}catch(Exception $ex){
			echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la obtencion dep reguntas'.$ex->getMessage()));
		}
		
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}

?>