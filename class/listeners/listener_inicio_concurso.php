<?php
  require_once '../PreguntasGeneradas.php';

  if(!isset($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['lanzada'])){
    die("Mal uso del listener de pregunts, sin algun parametros");
  }
  $idConcurso = $_GET['ID_CONCURSO'];
  $idRonda = $_GET['ID_RONDA'];
  // infinite loop until the data file is not modified
  $lastLanzada    = isset($_GET['lanzada']) ? $_GET['lanzada'] : 0;
  $generada = new PreguntasGeneradas();
  $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda); 
  $currentLanzada = $lanzadaBD[0]['LANZADA'];
  while ($currentLanzada <= $lastLanzada) // check if the data file has been modified
  {
    usleep(10000); // sleep 10ms to unload the CPU
    $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda); 
    $currentLanzada = $lanzadaBD[0]['LANZADA'];
  }
 
  // return a json array
  $response = array();
  $response['pregunta']= $lanzadaBD;
  $response['lanzada'] = $currentLanzada;
  echo json_encode($response);
  ?>