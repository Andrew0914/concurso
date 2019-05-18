<?php
   require_once dirname(__FILE__) . '/class/database/BaseTable.php';

   function generaQuerys($pregunta , $nuevaPregunta , $respuestaA , $respuestaB, $respuestaC,$respuestaD, $correcta){
      $select = "SELECT ID_PREGUNTA WHERE PREGUNTA = '%". $pregunta ."%'";
      $queryPregunta = "UPDATE preguntas SET PREGUNTA = '". $nuevaPregunta . "' WHERE PREGUNTA LIKE '%".$pregunta."%'";
      $queryResetRespuesta = "UPDATE respuestas SET ES_CORRECTA = 0 WHERE ID_PREGUNTA = (".$select.")";
      echo $queryPregunta
   }


?>