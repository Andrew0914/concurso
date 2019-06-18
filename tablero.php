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
	<link rel="stylesheet" type="text/css" href="css/libs/fontawesome/css/all.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content blanco content-lg">
	<section class="centrado">
		<br>
		<div class="row">
			<div class="col-md-4 offset-md-8">
				<?php 
					if($concurso['FECHA_CIERRE'] != '' && $concurso['FECHA_CIERRE'] != null){
						echo "<h4 style='color:#545454'>Concurso cerrado</h4>";
						echo "<br>";
						echo "<a href='panel' target='_self' style='text-decoration:underline'>Volver al panel</a>";
					}else{
						switch ($empate['estado']) {
							case 0:
								echo "<button class='btn btn-link'>Hubo un error , refresca la pagina</button>";
								break;
							case 1:
								echo "<button class='btn btn-geo' onclick='irDesempate(".$sesion->getOne(SessionKey::ID_CONCURSO).",".$tablero_master_id. ")'>Ir a desempate</button>";
								break;
							case 2:
								echo "<button class='btn btn-geo' onclick='cerrarConcurso(".$sesion->getOne(SessionKey::ID_CONCURSO).")'>Finalizar Concurso</button>";
								break;
							default:
								echo "<button class='btn btn-link'>Hubo un error , refresca la pagina</button>";
								break;
						}
					}
				?>
				<br>
				<i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading-s" style="display: none"></i>
			</div>
		</div>
		<h1>
            <img src="image/logo_geollin.png" />
        </h1>
		<h4 class="title">
			Concurso: <?php  echo $concurso['CONCURSO']; ?>
		</h4>
		<h5>
			Inicio : <?php echo $concurso['FECHA_INICIO']; ?>
		</h5>
		<br>
		<!-- TABLERO PUNTAJE -->
		<div class="row">
			<div class="col-md-12">
				<!-- TABERO FINAL / PUNTUACIONES GENERALES-->
				<div id="divmejores">
					<table class="table table-bordered table-geo" id="tbl-mejores" style="width: 100%;">
						<thead>
							<tr>
								<th>Posicion</th>
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
								$hastaPosicion = 0;
								if($ronda['IS_DESEMPATE']){
									$hastaPosicion = 3;
								}else{
									$hastaPosicion = 7;
								}
								foreach ($lugares as $l) {
									if($l['POSICION'] <= $hastaPosicion){
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
								<th><i class="fa fa-user"></i></th>
								<th>Ronda</th>
								<th><i class="fas fa-question-circle"></i></th>
								<th>Respondió</th>
								<th>Puntaje</th>
								<th>Roba Puntos</th>
								<th><i class="fa fa-clock"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$puntajes = $tableros['puntajes']['tablero'];
								foreach ($puntajes as $puntaje) {
									echo "<tr>";
									echo "<td>" . $puntaje['CONCURSANTE'] . '</td>';
									echo "<td>" . $puntaje['RONDA'] . '</td>';
									echo "<td><button class='btn btn-info' data-toggle='tooltip' data-placement='right' ";
									echo " onclick='verPregunta(\"".addslashes($puntaje['PREGUNTA'])."\",this)'><i class='fas fa-info-circle'></i>&nbsp;";
									echo $puntaje['PREGUNTA_POSICION'].'</button>';
									echo "<button class='btn btn-success ml-1' onclick='verRespuestaCorrecta(";
									echo $puntaje['ID_PREGUNTA']. ",this)'>";
									echo "<i class='fas fa-reply'></i> </button> </td>";
									if($puntaje['INCISO'] != '' and $puntaje['INCISO'] != null){
										echo "<td><b>".$puntaje['INCISO'].')&nbsp;</b>';
										if($puntaje['ES_IMAGEN'] == 1){
											echo '<img src="image/respuestas/'.$puntaje['RESPUESTA'].'"></td>';;
										}else{	
											echo $puntaje['RESPUESTA'].'</td>';
										}
									}else{
										echo "<td>Sin respuesta</td>";
									}
									echo "<td>".$puntaje['PUNTAJE'].'</td>';
									//roba puntos
									echo "<td>".$puntaje['PASO_PREGUNTAS'];
									if($puntaje['PASO'] == 1){
										echo '<br><i class="fas fa-step-forward"></i>&nbsp;'.$puntaje['CONCURSANTE_TOMO'].'</td>';
									}else if($puntaje['PASO'] == 2){
										echo '<br><i class="fas fa-times-circle"></i>&nbsp;'.$puntaje['CONCURSANTE_TOMO'].'</td>';
									}else{
										echo '</td>';
									}
									echo "<td>" . $puntaje['TIEMPO'] . '</td>';
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
					<button class="btn btn-danger" style="font-size: 24px" onclick="$('#mdl-fin-concurso').modal('hide')">&times;</button>
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
							echo "<tr> <th> Posición </th>";
							echo "<th> Concursante </th>";
							echo "<th> Puntaje </th> </tr>";
							echo "</thead>";
							foreach ($empatados as $empatado) {
								if($empatado['EMPATADO']){
									echo "<tr>";
									if($empatado['POSICION'] == 1){
										echo "<td><img src='image/gold_medal.png' width='35' height='35'></td>";
									}else if($empatado['POSICION']==2){
										echo "<td><img src='image/silver_medal.png' width='35' height='35'></td>";
									}else if($empatado['POSICION'] == 3){
										echo "<td><img src='image/bronze_medal.png' width='35' height='35'></td>";
									}else{
										echo "<td>".$empatado['POSICION'] . "</td>";
									}
									echo "<td>". $empatado['CONCURSANTE'] . "</td>";
									echo "<td>". $empatado['PUNTAJE_TOTAL'] . "</td>";
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
	<style>
		.tooltip-inner,.tooltip {
			max-width: 500px;
			width: 500px;
			max-height: 150px;
			height: auto;
			font-size: 18px;
			text-align: justify;
		}
	</style>
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/popper.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<script type="text/javascript">
		function mostrarResumen(){
			$("#divtablero").slideToggle(500);
		}

		function verPregunta(pregunta,boton){
			$(boton).attr("title",pregunta);
			$(boton).tooltip('show');
		}

		function verRespuestaCorrecta(idPregunta, boton){
			$.ajax({
				type: "GET",
				url: "class/Respuestas.php",
				data: { "functionRespuesta" : "verCorrecta" , "ID_PREGUNTA" : idPregunta},
				dataType: "json",
				success: function (response) {
					if(response.estado == 1){
						$(boton).attr("title", "RESPUESTA CORRECTA: (" + response.INCISO + ") "+ response.RESPUESTA);
						$(boton).tooltip('show');
					}
				},error:function(error){
					console.log(error);
				}
			});
		}
	</script>
	<!-- FIN SCRIPTS  -->
	<script type="text/javascript">
		$("#mdl-fin-concurso").modal();
	</script>
</body>
</html>