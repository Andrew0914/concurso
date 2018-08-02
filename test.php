<?php 

/**
 * Escribe el contenido en un archivo de terxto para pruebas
 * @param  [type] $txt [description]
 * @return [type]      [description]
 */
  function escribirPrueba($txt){
    $myfile = fopen("pruebas.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
  }
?>