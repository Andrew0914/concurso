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
<body class="content content-lg azul">
	<section>
		<div class="card-md centrado" >
			<h1 style="color: #545454" class="monserrat-bold"><b>Gracias</b></h1>
			<h4 id="mensaje_concurso">
				<?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?>
				<br><br>
				En cuanto todo este listo el moderador comenzara el concurso
			</h4>
		</div>
		<form  name="form-individual1" id="form-individual1">
			<!-- CRONOMETRO -->
			<div class="row">
				<div class="col-md-4 offset-md-4 centrado">
					<svg id="animated" viewbox="0 0 100 100">
					  <circle cx="50" cy="50" r="45" fill="#FFF"/>
					  <path id="progress" stroke-linecap="round" stroke-width="4" stroke="rgb(180,185,210)" fill="none"
					        d="M50 10
					           a 40 40 0 0 1 0 80
					           a 40 40 0 0 1 0 -80">
					  </path>
					  <text id="cronometro" x="50" y="50" text-anchor="middle" dy="7" font-size="11">00:00</text>
					</svg>	
				</div>
			</div>
			<!-- CRONOMETRO -->
			<div class='preguntadisplay'></div>
		</form>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/snap.svg-min.js"></script>
	<script type="text/javascript" src="js/cronometro.js"></script>
	<script type="text/javascript" src="js/individua_ronda1.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>