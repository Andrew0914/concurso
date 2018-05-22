<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
 ?>
<head>
	<meta charset="utf-8">
	<title>Inicio concursante</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	<section class="contenido">
		<h1> Bienvenido: <?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?></h1>
		<h3 id="mensaje_concurso">
			La ronda 1 del concurso individual aun no comienza por favor espera a que el moderador inicie el concurso
			[DEMO esta incompleto aun, las preguntas apareceran de a una con su limite de tiempo]
		</h3>
		<div id="preguntas"></div>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/individua_ronda1.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>