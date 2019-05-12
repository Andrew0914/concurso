<?php
    require_once dirname(__FILE__).'/class/util/Response.php';

    $response['estado'] = 1;
    $response['pregunta'] = ['algo'=>1];
    $response['pregunta']['respuestas'] = ['algo'=>1];
    $response['mensaje'] = 'Pregunta obtenida exitosamente';
    echo json_encode($response);
    echo "<br>";
    $pregunta = ['pregunta' => ['algo'=>1]];
    $pregunta['pregunta']['respuestas'] = ['algo' => 1]; 
    $testResponse = new Response();
    echo json_encode($testResponse->success($pregunta,'Pregunta obtenida exitosamente')  );
?>