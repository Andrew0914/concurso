<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Concurso.php';
	require_once 'class/TableroPuntaje.php';
	$concurso = new Concurso();
	$sesion = new Sesion();
	$tablero = new TableroPuntaje();
	$concurso = $concurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
 ?>
<head>
	<meta charset="utf-8">
	<title>Panel Concurso</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content blanco content-lg">
	<section class="centrado">
		<br>
		<div class="row">
			<div class="col-md-2 offset-md-10">
				<button class="btn btn-geo btn-block" id="btnObtenerPuntaje" onclick="location.reload();" >
					Actualizar tablero 
				</button>
			</div>
		</div>
		<h1 class="title">
			Concurso: <?php  echo $concurso['CONCURSO']; ?>
		</h1>
		<h4>
			Inicio : <?php echo $concurso['FECHA_INICIO']; ?>
		</h4>
		<br>
		<!-- TABLERO PUNTAJE -->
		<div class="row">
			<div class="col-md-12">
				<!-- TABERO FINAL / PUNTUACIONES GENERALES-->
				<div id="divmejores">
					<table class="table table-bordered table-geo" id="tbl-mejores" style="width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th>Concursante</th>
								<th>Puntaje Total</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$response = $tablero->getMejoresPuntajes($sesion->getOne(SessionKey::ID_CONCURSO),$sesion->getOne(SessionKey::ID_RONDA));
								$mejores = $response['mejores'];
								foreach ($mejores as $mejor) {
									echo "<tr>";
									if($mejor['lugar'] == 1){
										echo "<td><img src='image/gold_medal.png'></td>";
									}else if($mejor['lugar']==2){
										echo "<td><img src='image/silver_medal.png'></td>";
									}
									else if($mejor['lugar']==3){
										echo "<td><img src='image/bonze_medal.png'></td>";
									}else{
										echo "<td>".$mejor['lugar']."<small> lugar</small></td>";
									}
									echo "<td>".$mejor['CONCURSANTE'] . '</td>';
									echo "<td>".$mejor['totalPuntos'].'</td>';
									echo "</tr>";	
								}
							 ?>
						</tbody>
					</table>
				</div>
				<!-- TABERO FINAL / PUNTUACIONES GENERALES-->
				<button class="btn btn-lg btn-geo" onclick="mostrarResumen()">
					Ver Resumen Global de Puntaciones
				</button>
				<br>
				<!-- TABLERO DE RESUMEN DETALLADO-->
				<div id="divtablero" style="display: none;">
					<table class="table table-bordered table-geo" id="tbl-puntaje" style="width: 100%;">
						<thead>
							<tr>
								<th>Concursante</th>
								<th># Pregunta</th>
								<th>Respuesta</th>
								<th>Paso</th>
								<th>Puntaje</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$response = $tablero->getTableroDisplay($sesion->getOne(SessionKey::ID_CONCURSO),$sesion->getOne(SessionKey::ID_RONDA));
								$tableros = $response['tablero'];
								foreach ($tableros as $tab) {
									echo "<tr>";
									echo "<td>" . $tab['CONCURSANTE'] . '</td>';
									echo "<td>".$tab['PREGUNTA_POSICION'].'</td>';
									echo "<td><b>".$tab['INCISO'].')&nbsp;</b>';
									if($tab['ES_IMAGEN'] == 1){
										echo '<img src="image/respuestas/'.$tab['RESPUESTA'].'"></td>';;
									}else{	
										echo $tab['RESPUESTA'].'</td>';
									}
									echo "<td>".$tab['PASO_PREGUNTA'].'</td>';
									echo "<td>".$tab['PUNTAJE'].'</td>';
									echo "</tr>";	
								}
							 ?>
						</tbody>
					</table>
				</div>
				<!-- TABLERO DE RESUMEN DETALLADO-->
			</div>
		</div>
		<!--/TABLERO PUNTAJE -->
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<script type="text/javascript">
		function mostrarResumen(){
			$("#divtablero").slideToggle(500);
		}
	</script>
	<!-- FIN SCRIPTS  -->
</body>
</html>