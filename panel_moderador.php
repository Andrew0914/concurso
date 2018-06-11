<!DOCTYPE html>
<html>
<?php 
	require_once 'class/Rondas.php';
	require_once 'class/Etapas.php';
	require_once 'class/Categorias.php';
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA))
 ?>
<head>
	<meta charset="utf-8">
	<title>Panel Moderador</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="blanco content content-md">
	<div class="card-lg">
		<div class="row">
			<div class="col-md-4 offset-md-4">
				<img src="image/logo_geollin.png" class="img-thumbnail">
			</div>
		</div>
		<br>
		<!-- INFORMACION GENERAL-->
		<div class="row">
			<div class="col-md-6">
				<b class="monserrat-bold">Concurso:</b> <?php echo $sesion->getOne(SessionKey::CONCURSO); ?>
				<br>
				<b class="monserrat-bold">Etapa:</b> <?php echo $etapa['ETAPA']; ?>
			</div>
			<div class="col-md-6">
				<b class="monserrat-bold">Ronda elegida:</b> <?php echo $ronda['RONDA']; ?>
			</div>
		</div>
		<hr>
		<!-- TERMINA INFORMACION GENERAL -->
		<div class="row">
			<div class="col-md-8">
				<b class="monserrat-bold">Generar Preguntas</b>
				<select class="select-geo" style="margin: 0;width: 100%" id="ID_CATEGORIA">
					<option value="">Selecciona categoria</option>
					<?php 
						$categoria = new Categorias();
						$categorias = $categoria->getCategorias()['categorias'];
						foreach ($categorias as $cat) {
							echo "<option value='".$cat['ID_CATEGORIA']."'>" .$cat['CATEGORIA']. "</option>";
						}
					 ?>
				</select>
			</div>
			<div class="col-md-4">
				<br>
				<button class="btn-geo" onclick="generaPreguntas(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO).','.$ronda['ID_RONDA']; ?>)">
					Generar
				</button>
			</div>
		</div>
	</div>
	<!-- SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/panel_moderador.js"></script>
</body>
</html>