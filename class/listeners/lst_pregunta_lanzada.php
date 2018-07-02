<?php
	require_once '../Concurso.php';
	require_once '../RondasLog.php';
	require_once '../PreguntasGeneradas.php';
	require_once '../Respuestas.php';

	if(!isset($_GET['lanzada'],$_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['ID_CATEGORIA'])){
		die("Mal uso del listener de pregunts, sin algun parametros");
	}
	$objRespuesta = new Respuestas();
	$idConcurso = $_GET['ID_CONCURSO'];
	$idRonda = $_GET['ID_RONDA'];
	$idCategoria = $_GET['ID_CATEGORIA'];
	// infinite loop until the data file is not modified
	$lastLanzada    = isset($_GET['lanzada']) ? $_GET['lanzada'] : 0;
	$generada = new PreguntasGeneradas();
	$lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria); 
	$lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
	$currentLanzada = $lanzadaBD[0]['LANZADA'];
	while ($currentLanzada <= $lastLanzada) {
		usleep(15000); // sleep 10ms to unload the CPU
		$lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria);
		$lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
		$currentLanzada = $lanzadaBD[0]['LANZADA'];
	}
	$concurso = new Concurso();
	$concurso = $concurso->getConcurso($idConcurso);
	$log = new RondasLog();
	// return a json array
	$response = array();
	$response['concurso']= $concurso;
	$response['pregunta']= $lanzadaBD;
	$response['termina_categoria'] = $log->rondasTerminadasCategoria($idConcurso, $idCategoria);
	$response['terminaron_todo'] = $log->rondasTerminadas($idConcurso);
	$response['lanzada'] = $currentLanzada;
	echo json_encode($response);
?>