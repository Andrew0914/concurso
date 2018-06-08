<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
 ?>
<head>
	<meta charset="utf-8">
	<title>Individual::Ronda2</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content content-lg azul">
	<section>
		<div class="card-md centrado" id="card-inicio">
			<h1 style="color: #545454" class="monserrat-bold"><b>Gracias</b></h1>
			<h4 id="mensaje_concurso">
				<?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?>
				<br><br>
				Has concluido la ronda 1 de la etapa individual, en cuanto todo este listo el moderador dará inicio a la ronda 2.
			</h4>
		</div>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/snap.svg-min.js"></script>
	<script type="text/javascript" src="js/cronometro.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>