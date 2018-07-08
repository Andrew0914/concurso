<?php
  require_once '../PreguntasGeneradas.php';
  require_once '../Respuestas.php';
  require_once '../Reglas.php';
  require_once '../RondasLog.php';
  require_once '../Concurso.php';
  if(!isset($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['lanzada'])){
    die("Mal uso del listener de pregunts, sin algun parametros");
  }
  $objRespuesta = new Respuestas();
  $idConcurso = $_GET['ID_CONCURSO'];
  $idRonda = $_GET['ID_RONDA'];
  $idCategoria = $_GET['ID_CATEGORIA'];
  $es_desempate = false;
  if($idCategoria == 'desempate'){
    $es_desempate = true;
  }
  // infinite loop until the data file is not modified
  $lastLanzada    = isset($_GET['lanzada']) ? $_GET['lanzada'] : 0;
  $generada = new PreguntasGeneradas();
  $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria,$es_desempate); 
  $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
  $currentLanzada = $lanzadaBD[0]['LANZADA'];
  $log = new RondasLog();
  $response = array();
  while ($currentLanzada <= $lastLanzada) // check if the data file has been modified
  {
    sleep(1); // sleep 10ms to unload the CPU
    $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria,$es_desempate);
    $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
    $currentLanzada = $lanzadaBD[0]['LANZADA'];
  }
  // return a json array
  $response['pregunta']= $lanzadaBD;
  $response['lanzada'] = $currentLanzada;
  echo json_encode($response);
?>