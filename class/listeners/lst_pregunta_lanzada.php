<?php
  require_once '../PreguntasGeneradas.php';
  require_once '../Respuestas.php';
  require_once '../Reglas.php';
  require_once '../Turnos.php';

  if(!isset($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['lanzada'])){
    die("Mal uso del listener de pregunts, sin algun parametros");
  }
  $objRespuesta = new Respuestas();
  $idConcurso = $_GET['ID_CONCURSO'];
  $idRonda = $_GET['ID_RONDA'];
  // infinite loop until the data file is not modified
  $lastLanzada    = isset($_GET['lanzada']) ? $_GET['lanzada'] : 0;
  $generada = new PreguntasGeneradas();
  $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda); 
  $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
  $currentLanzada = $lanzadaBD[0]['LANZADA'];
  while ($currentLanzada <= $lastLanzada) // check if the data file has been modified
  {
    usleep(10000); // sleep 10ms to unload the CPU
    $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda);
    $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
    $currentLanzada = $lanzadaBD[0]['LANZADA'];
  }
  $regla = new Reglas();
  $regla = $regla->getReglasByRonda($idRonda)[0];
  // return a json array
  $response = array();
  $response['pregunta']= $lanzadaBD;
  $response['lanzada'] = $currentLanzada;
  // si tiene paso de turno la pregunta regresa con el turno de quien peude contestar
  if($regla['TIENE_TURNOS'] == 1 ){
    $turno = new Turnos();
    $turno = $turno->getLast($idConcurso, $idRonda);
    $response['TIENE_TURNOS'] = 1;
    $response['LAST_TURNO'] = $turno;
  }
  echo json_encode($response);
?>