<?php


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
    $route->add('lanzador_2dn_grupal' , '../lanzador_2dn_grupal.php');
    $route->add('grupal1' , '../grupal_ronda1.php');
    $route->add('grupal2' , '../grupal_ronda2.php');
    $route->add('grupal_desempate','../grupal_desempate.php');
    $route->add('obtener_excel','../obtener-excel.php');
	$route->submit();
?>