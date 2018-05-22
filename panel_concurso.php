<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
 ?>
<head>
	<meta charset="utf-8">
	<title>Panel Concurso</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	<section class="contenido-tablero">
		<h1>
			<?php  echo $sesion->getOne(SessionKey::CONCURSO); ?>
		</h1>
		<!-- TABLERO PUNTAJE -->
		<table class="table table-bordered" id="tbl-puntaje" style="width: 100%">
			<thead>
				<tr>
					<th>Concursante</th>
					<th># Pregunta</th>
					<th>Pregunta</th>
					<th>Respuesta</th>
					<th>Paso</th>
					<th>Puntaje</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<!--/TABLERO PUNTAJE -->
		<!--INICIO DEL CONCURSO-->
		<form id="form-iniciar-concurso">
			<input type="hidden" name="ID_CONCURSO" id="ID_CONCURSO" value="<?php  echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>"/>
			<button type='button' class="btn btn-lg btn-primary" onclick="iniciarConcurso($('#form-iniciar-concurso'))">
				Iniciar concurso
			</button>
		</form>
		<!--INICIO DEL CONCURSO-->
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>