<?php

require_once 'class/util/Route.php';
	$route = new Route();

	// rutas de la app
    $route->add('crear', '../crear_concurso.php');
    $route->add('panel', '../panel_concurso.php');
    $route->add('inicio', '../inicio_concursante.php');
    $route->add('individual_ronda1', '../concurso_etapa1_ronda1.php');
    $route->add('moderador', '../inicio_moderador.php');
    
	$route->submit();
?>