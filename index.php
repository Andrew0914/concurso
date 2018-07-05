<?php

require_once 'class/util/Route.php';
	$route = new Route();

	// rutas de la app
	$route->add('/', '../inicio_moderador.php');
    $route->add('crear', '../crear_concurso.php');
    $route->add('panel', '../panel_moderador.php');
    $route->add('inicio', '../inicio_concursante.php');
    $route->add('moderador', '../inicio_moderador.php');
    $route->add('leer_preguntas', '../lanzador_preguntas.php');
    $route->add('tablero', '../tablero.php');
    $route->add('404', '../404.php');
    $route->add('individual1', '../individual_ronda1.php');
    $route->add('individual2', '../individual_ronda2.php');
    $route->add('individual_desempate', '../individual_desempate.php');
    $route->add('inicio_desempate', '../inicio_desempate.php');
    $route->add('concurso_finalizado' , '../fin_concurso.php');
    $route->add('lanzador_desempate' , '../lanzador_desempate.php');
    $route->add('tablero_desempate' , '../tablero_desempate.php');
	$route->submit();
?>