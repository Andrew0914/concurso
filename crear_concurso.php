<!DOCTYPE html>
<html lang="es">
<?php 
	require_once dirname(__FILE__) . '/class/Etapas.php';
 ?>
<head>
	<meta charset="utf-8">
	<title>Geollin</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	<!-- CABECERA-->
	<header class="contenido">
		<h1>
			Geollin concurso
		</h1>
	</header>
	<!-- /CABECERA-->
	<!-- CONTENIDO-->
	<section class="contenido">
		<form id="form-genera-concurso" class="form-group">
			<label for="CANTIDAD_PARTICIPANTES">Cantidad de participantes</label>
			<input type="number" id="CANTIDAD_PARTICIPANTES" name="CANTIDAD_PARTICIPANTES" class="form-control" value="1" />
			<label for="ID_ETAPA">Etapa de concurso</label> 
			<select id="ID_ETAPA" name="ID_ETAPA" class="form-control" >
				<?php 
					$etapas = new Etapas();
					$etapas = $etapas->getEtapas();
					foreach ( $etapas as  $etapa) {
						echo "<option value='".$etapa['ID_ETAPA']."'>".$etapa['ETAPA'] . "</option>";
					}
				 ?>
			</select>
			<label for="CONCRUSO">Nombre del cocnurso</label>
			<input type="text" class="form-control" id="CONCURSO" name="CONCURSO">
			<br>
			<table class="table table-bordered" id="tbl-concursantes">
				<thead>
					<tr>
						<th>Concursante/Equipo</th>
						<th>Password</th>
						<th>Posicion</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<button type="button" name="btn_generar_concursantes" id="btn_generar_concursantes" class="btn btn-info" onclick="generaConcursantes()">
				Generar concursantes
			</button>
			<button type="button" name="btn_generar_concurso" id="btn_generar_concurso" class="btn btn-primary" onclick="generarConcurso($('#form-genera-concurso'))" style="display: none;">
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