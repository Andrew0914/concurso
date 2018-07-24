<?php 
  function escribirPrueba($txt){
    $myfile = fopen("pruebas.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
  }
?>