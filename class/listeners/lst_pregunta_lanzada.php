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
   $todas_lanzadas = 0;
   $tiempo_muerto = 0;
   $bol_tiempo_muerto = 0;
   while ($currentLanzada <= $lastLanzada) // check if the data file has been modified
   {
      sleep(1); // sleep 10ms to unload the CPU
      if($tiempo_muerto >= 30){
         $bol_tiempo_muerto = 1;
         break;
      }
      $lanzadaBD = $generada->ultimaLanzada($idConcurso,$idRonda,$idCategoria,$es_desempate,$nivel_empate);
      $lanzadaBD['respuestas'] = $objRespuesta->getRespuestasByPregunta($lanzadaBD[0]['ID_PREGUNTA']);
      $currentLanzada = $lanzadaBD[0]['LANZADA'];
      if($lastLanzada >= $ronda['PREGUNTAS_POR_CATEGORIA']){
         $todas_lanzadas = 1;
         break;
      }
      $tiempo_muerto++;
   }
   //devolvemos la informacio de pregunta lanzada
   $response['todas_lanzadas'] = $todas_lanzadas;
   $response['tiempo_muerto'] = $bol_tiempo_muerto;
   $response['pregunta']= $lanzadaBD;
   $response['lanzada'] = $currentLanzada;
   echo json_encode($response);
   // liberamos memoria
   unset($objRespuesta);
   unset($idConcurso);
   unset($idRonda);
   unset($nivel_empate);
   unset($idCategoria);
   unset($es_desempate);
   unset($lastLanzada);
   unset($generada);
   unset($lanzadaBD);
   unset($lanzadaBD);
   unset($currentLanzada);
   unset($log);
   unset($response);
   unset($objRonda);
   unset($ronda);
   unset($todas_lanzadas);
   unset($tiempo_muerto);
   unset($bol_tiempo_muerto);
?>