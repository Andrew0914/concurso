<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	require_once dirname(__FILE__) . '/../PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/../Respuestas.php';

	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		
		while ($concurso['INICIO_CONCURSO'] == 0 ) {
			usleep(5000);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		}

		try{
			$preguntasGeneradas = new PreguntasGeneradas();
			$preguntas = $preguntasGeneradas->getPreguntasByConcursoRonda($concurso['ID_CONCURSO'],1);
			$respeusta = new Respuestas();
			
			for ($cont = 0 ; $cont < count($preguntas); $cont++) {
				$preguntas[$cont]['respuestas'] = $respeusta->getRespuestasByPregunta($preguntas[$cont]['ID_PREGUNTA']);
			}

			echo json_encode(array('estado'=>1,'mensaje'=>'Preguntas obtenidas, COMIENZA EL CONCURSO', 'preguntas'=>$preguntas));
		}catch(Exception $ex){
			echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la obtencion dep reguntas'.$ex->getMessage()));
		}
		
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}

?>