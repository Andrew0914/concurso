<?php
   require_once dirname(__FILE__) . '/class/TableroPosiciones.php';
   require_once dirname(__FILE__) . '/class/TableroPuntaje.php';
   $tablero = new TableroPosiciones();
   //$tablero = new TableroPuntaje();
   echo json_encode($tablero->generaPosiciones(6 , TRUE));
   //echo json_encode($tablero->getMejoresPuntajes(6,false));
?>