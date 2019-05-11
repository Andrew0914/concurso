<?php
   $arrays = [['grado'=>1 , 'cantidad'=> 30 ] ,  ['grado'=>2 , 'cantidad'=> 20 ], ['grado'=>2 , 'cantidad'=> 10 ]];

    $filtrado = array_filter($arrays, function ($var) {
        return ($var['grado'] == 1);
    });

   echo json_encode($filtrado);
?>