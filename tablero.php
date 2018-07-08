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
	$empate = $tablero->esEmpate($sesion->getOne(SessionKey::ID_CONCURSO));
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
			<div class="col-md-2 offset-md-8">
				<button class="btn btn-geo btn-block" id="btnObtenerPuntaje" onclick="location.reload();" >
					Actualizar tablero
				</button>
			</div>
			<div class="col-md-2">
				<?php 
					switch ($empate['estado']) {
						case 0:
							echo "<button class='btn btn-link'>Hubo un error , refresca la pagina</button>";
							break;
						case 1:
							echo "<button class='btn btn-geo' onclick='irDesempate(".$sesion->getOne(SessionKey::ID_CONCURSO).")'>Ir a desempate</button>";
							break;
						case 2:
							echo "<button class='btn btn-geo' onclick='cerrarConcurso(".$sesion->getOne(SessionKey::ID_CONCURSO).")'>Finalizar Concurso</button>";
							break;
						default:
							echo "<button class='btn btn-link'>Hubo un error , refresca la pagina</button>";
							break;
					}
				?>
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
								$response = $tablero->getMejoresPuntajes($sesion->getOne(SessionKey::ID_CONCURSO));
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
								<th>Ronda</th>
								<th>Categoria</th>
								<th> # </th>
								<th> Pregunta </th>
								<th>Respuesta</th>
								<th>Puntaje</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$response = $tablero->getResultados($sesion->getOne(SessionKey::ID_CONCURSO));
								$tableros = $response['tablero'];
								foreach ($tableros as $tab) {
									echo "<tr>";
									echo "<td>" . $tab['CONCURSANTE'] . '</td>';
									echo "<td>" . $tab['RONDA'] . '</td>';
									echo "<td>" . $tab['CATEGORIA'] .'</td>';
									echo "<td>".$tab['PREGUNTA_POSICION'].'</td>';
									echo "<td>".$tab['PREGUNTA'].'</td>';
									echo "<td><b>".$tab['INCISO'].')&nbsp;</b>';
									if($tab['ES_IMAGEN'] == 1){
										echo '<img src="image/respuestas/'.$tab['RESPUESTA'].'"></td>';;
									}else{	
										echo $tab['RESPUESTA'].'</td>';
									}
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
	<!-- MODAL FINALIZACION CONCURSO-->
	<div class="modal" tabindex="-1" role="dialog" id="mdl-fin-concurso">
		<div class="modal-dialog blanco modal-lg" role="document">
			<div class="modal-content blanco">
				<div class="modal-header">
					<h3 class="modal-title">Fin del concurso</h3>
					<button class="btn" style="font-size: 24px" onclick="$('#mdl-fin-concurso').modal('hide')">&times;</button>
				</div>
				<div class="modal-body centrado">
					<hr>
					<h4 class="monserrat-bold"><?php echo $empate['mensaje']; ?></h4>
					<hr>
					<?php 
						if($empate['estado'] == 1){
							$empatados = $empate['empatados'];
							echo "<table class='table table-sm table-bordered table-geo'>";
							echo "<thead>";
							echo "<tr> <th> # </th>";
							echo "<th> Concursante </th>";
							echo "<th> Puntaje </th> </tr>";
							echo "</thead>";
							foreach ($empatados as $e) {
								echo "<tr>";
								echo "<td>". $e['ID_CONCURSANTE'] . "</td>";
								echo "<td>". $e['CONCURSANTE'] . "</td>";
								echo "<td>". $e['totalPuntos'] . "</td>";
								echo "</tr>";
							}
							echo "</table>";
						}
					?>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
	<!-- MODAL FINALIZACION CONCURSO-->
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
	<script type="text/javascript">
		$("#mdl-fin-concurso").modal();
	</script>
</body>
</html>