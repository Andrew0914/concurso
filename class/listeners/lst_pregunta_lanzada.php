<?php
  require_once '../PreguntasGeneradas.php';
  require_once '../Respuestas.php';
  require_once '../Reglas.php';
  require_once '../RondasLog.php';
  require_once '../Concurso.php';
  require_once '../Rondas.php';
  error_reporting(0);
  if(!isset($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['lanzada'])){
    die("Mal uso del listener de pregunts, sin algun parametros");
  }
  $objRespuesta = new Respuestas();
  $idConcurso = $_GET['ID_CONCURSO'];
  $idRonda = $_GET['ID_RONDA'];
  $nivel_empate = $_GET['NIVEL_EMPATE'];
  $idCategoria = $_GET['ID_CATEGORIA'];
  $es_desempate = $_GET['IS_DESEMPATE'];
  // infinite loop until the data file is not modified
  $lastLanzada    = isset($_GET['lanzada']) ? $_GET['lanzada'] : 0;
  $generada = new PreguntasGeneradas();
  $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria,$es_desempate,$nivel_empate); 
  $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
  $currentLanzada = $lanzadaBD[0]['LANZADA'];
  $log = new RondasLog();
  $response = array();
  $objRonda = new Rondas(); 
  $ronda = $objRonda->getRonda($idRonda);
  $tiempo_muerto = 0;
  $response['todas_lanzadas'] = 0;
  $response['tiempo_muerto'] = 0;
  while ($currentLanzada <= $lastLanzada)
  {
    sleep(1);
    // si lleva 10 segundos sin salir del while salimos para no desgastar los recursos
    if($tiempo_muerto >= 30){
      $response['tiempo_muerto'] = 1;
      break;
    }
    $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria,$es_desempate,$nivel_empate);
    $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
    $currentLanzada = $lanzadaBD[0]['LANZADA'];
    // si ya es la ultima pregunta salimos del ciclo para no desgastar recursos
    if($lastLanzada >= $ronda['PREGUNTAS_POR_CATEGORIA']){
      $response['todas_lanzadas']= 1;
      break;
    }
    $tiempo_muerto++;
  }
  $response['pregunta']= $lanzadaBD;
  $response['lanzada'] = $currentLanzada;
  echo json_encode($response);
?>