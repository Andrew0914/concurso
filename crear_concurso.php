<!DOCTYPE html>
<html lang="es">
<?php 
	require_once dirname(__FILE__) . '/class/Etapas.php';
	require_once dirname(__FILE__) . '/class/util/Sesion.php';

	$sesion = new Sesion();
	$sesion->kill();
 ?>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Geollin</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content blanco centrado content-sm">
	<!-- CABECERA-->
	<header>
		<h1>
			<img src="image/logo_geollin.png">
		</h1>
	</header>
	<br>
	<!-- /CABECERA-->
	<!-- CONTENIDO-->
	<section>
		<form id="form-genera-concurso" class="form-group centrado">
			<label for="CANTIDAD_PARTICIPANTES"><b>Cantidad de participantes</b></label>
			<input type="text" id="CANTIDAD_PARTICIPANTES" name="CANTIDAD_PARTICIPANTES" class="form-control" value="1" />
			<br>
			<label for="ID_ETAPA"><b>Etapa de concurso</b></label> 
				<select id="ID_ETAPA" name="ID_ETAPA" class="select-geo">
					<?php 
						$etapas = new Etapas();
						$etapas = $etapas->getEtapas();
						foreach ( $etapas as  $etapa) {
							echo "<option value='".$etapa['ID_ETAPA']."'>".$etapa['ETAPA'] . "</option>";
						}
					 ?>
				</select>
			<br>
			<label for="CONCRUSO"><b>Nombre del cocnurso</b></label>
			<input type="text" class="form-control" id="CONCURSO" name="CONCURSO" placeholder="Escribir..">
			<br>
			<table class="table table-bordered table-geo" id="tbl-concursantes">
				<thead>
					<tr>
						<th>Concursante/Equipo</th>
						<th>Password</th>
						<th>Posicion</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<button type="button" name="btn_generar_concursantes" id="btn_generar_concursantes" class="btn btn-geo" onclick="generaConcursantes()">
				Generar concursantes
			</button>
			<button type="button" name="btn_generar_concurso" id="btn_generar_concurso" class="btn btn-geo" onclick="generarConcurso($('#form-genera-concurso'))" style="display: none;">
				Generar concurso
			</button>
		</form>
	</section>
	<!-- / CONTENIDO-->
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>