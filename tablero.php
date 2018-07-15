<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Concurso.php';
	require_once 'class/TableroPuntaje.php';
	require_once 'class/TableroPosiciones.php';
	require_once 'class/Rondas.php';
	$concurso = new Concurso();
	$sesion = new Sesion();
	$tablero = new TableroPuntaje();
	$tabPosicion = new TableroPosiciones();
	$concurso = $concurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($concurso['ID_RONDA']);
	if(!isset($_GET['id_master'])){
		header('Location: inicio');
	}
	$tablero_master_id = $_GET['id_master'];
	$empate = $tabPosicion->esEmpate($tablero_master_id);
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
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$posiciones = new TableroPosiciones();
								$tableros = $posiciones->getTableros($sesion->getOne(SessionKey::ID_CONCURSO),$tablero_master_id,$ronda['IS_DESEMPATE']);
								$lugares = $tableros['posiciones'];
								foreach ($lugares as $l) {
									echo "<tr>";
									if($l['POSICION'] == 1){
										echo "<td><img src='image/gold_medal.png'></td>";
									}else if($l['POSICION']==2){
										echo "<td><img src='image/silver_medal.png'></td>";
									}else if($l['POSICION'] == 3){
										echo "<td><img src='image/bronze_medal.png'></td>";
									}else{
										echo "<td>".$l['POSICION'] . "</td>";
									}
									echo "<td>" . $l['CONCURSANTE'] .'</td>';
									echo "<td>" . $l['PUNTAJE_TOTAL'] .'</td>';
									if($l['EMPATADO'] == 1){
										echo "<td>EMPATADO</td>";
									}else{
										echo "<td></td>";
									}
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
								$puntajes = $tableros['puntajes']['tablero'];
								foreach ($puntajes as $puntaje) {
									echo "<tr>";
									echo "<td>" . $puntaje['CONCURSANTE'] . '</td>';
									echo "<td>" . $puntaje['RONDA'] . '</td>';
									echo "<td>" . $puntaje['CATEGORIA'] .'</td>';
									echo "<td>".$puntaje['PREGUNTA_POSICION'].'</td>';
									echo "<td>".$puntaje['PREGUNTA'].'</td>';
									echo "<td><b>".$puntaje['INCISO'].')&nbsp;</b>';
									if($puntaje['ES_IMAGEN'] == 1){
										echo '<img src="image/respuestas/'.$puntaje['RESPUESTA'].'"></td>';;
									}else{	
										echo $puntaje['RESPUESTA'].'</td>';
									}
									echo "<td>".$puntaje['PUNTAJE'].'</td>';
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
								if($e['EMPATADO']){
									echo "<tr>";
									echo "<td>". $e['ID_CONCURSANTE'] . "</td>";
									echo "<td>". $e['CONCURSANTE'] . "</td>";
									echo "<td>". $e['PUNTAJE_TOTAL'] . "</td>";
									echo "</tr>";
								}
								
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