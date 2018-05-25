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
<body class="content content-md azul">
	<section>
		<div class="card centrado">
			<h2 style="color: #545454"><b>Gracias</b></h2>
			<h4 id="mensaje_concurso">
				<?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?>
				<br><br>
				En cuanto todo este listo el moderador comenzara el concurso
			</h4>
		</div>
		<form  name="form-individual1" id="form-individual1">
			<table class="table table-geo">
				<tr><td colspan="4" id="cronometro"></td></tr>
			</table>
		</form>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/individua_ronda1.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>