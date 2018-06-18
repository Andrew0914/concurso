<!DOCTYPE html>
<html>
<!--PROCESAMIENTO-->
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/PreguntasGeneradas.php';
	require_once 'class/Rondas.php';
	require_once 'class/Etapas.php';
	require_once 'class/RondasLog.php';
	$sesion = new Sesion();
	$generadas = new PreguntasGeneradas();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA));
	$segundosPorPregunta = $ronda['SEGUNDOS_POR_PREGUNTA'];
	$idConcurso = $sesion->getOne(SessionKey::ID_CONCURSO);
	$idRonda = $sesion->getOne(SessionKey::ID_RONDA);
	// iniciamos la ronda
	$log = new RondasLog();
	if(!$log->iniciarRonda($idConcurso,$idRonda)){
		die('No pudimos iniciar la ronda , vuelve a intentarlo por vaor <a href="moderador">Click aqui</a>');
	}
 ?>
<head>
	<meta charset="utf-8">
	<title>Leer pregunta</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content content-lg azul">
	<section class="card-lg">
		<input type="hidden" id="ID_CONCURSO" name="ID_CONCURSO" value="<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>">
		<input type="hidden" id="ID_RONDA" name="ID_RONDA" value="<?php echo $sesion->getOne(SessionKey::ID_RONDA); ?>">
		<!-- INFORMACION GENERAL-->
		<div class="row">
			<div class="col-md-4">
				<img src="image/logo_geollin.png" class="img-thumbnail">
			</div>
		</div>
		<br>
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
		<!-- INFORMACION GENERAL-->
		<!-- RECARGAR PREGUNTAD-->
		<div class="row">
			<div class="col-md-2 offset-md-8">
				<a href="tablero" class="btn btn-sm btn-link" target="_blank">
					<u> Ver Puntajes</u>
				</a>
			</div>
			<div class="col-md-2">
				<button class="btn btn-sm btn-link" onclick="location.reload()">
					<u>Recargar preguntas</u>
				</button>
			</div>
		</div>
		<br>
		<!-- RECARGAR PREGUNTAD-->
		<!-- PREGUNTAS GENERADAS-->
		<div class="row">
			<div class="col-md-12">
				<table class="table table-sm table-bordered table-striped table-geo">
					<thead>
						<tr>
							<th>#Pregunta</th>
							<th>Pregunta</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$preguntas = $generadas->getPreguntasByConcursoRonda($idConcurso,$idRonda);
							$onclick = "";
							foreach ($preguntas as $pregunta) {
								echo "<tr>";
								echo "<td>" . $pregunta['PREGUNTA_POSICION']. "</td>";
								echo "<td>" . $pregunta['PREGUNTA']. "</td>";
								$onclick = " onclick='leer(\"".addslashes ($pregunta['PREGUNTA'])."\",";
								$onclick .= $pregunta['ID_PREGUNTA']. ",";
								$onclick .= $pregunta['ID_GENERADA'].")'"; 
								$button = "<td><button class='btn-geo'".$onclick.">Leer</button></td>";
								if($pregunta['HECHA'] == 1){
									$button= "<td><button class='btn btn-sm btn-dark'>HECHA</button></td>";
								}
								echo $button;
								echo  "</tr>";
							}
						 ?>
					</tbody>
				</table>
			</div>
			<div class="row">
				<div class="col-md-12 centrado">
					<a href="tablero" class="btn btn-sm btn-link" target="_blank">
						<u>Ver Puntajes</u>
					</a>
				</div>
			</div>
		</div>
		<!-- PREGUNTAS GENERADAS-->
	</section>
	<!--MODAL LEER-->
	<div class="modal fade" id="mdl-leer-pregunta" role="dialog">
	    <div class="modal-dialog modal-lg">
	    	<div class="modal-content blanco">
	        	<div class="modal-header">
		          	<h5 class="modal-title">Leer pregunta</h5>
	        	</div>
		        <div class="modal-body">
		         	<div class="row">
			          	<div class="col-md-12">
			          		<h4 id="p-pregunta" class="monserrat-bold centrado"></h4>
			          	</div>
		          	</div>
		          <!-- CRONOMETRO -->
					<div class="row" id="cronometro-content" style="display: none">
						<div class="col-md-8 offset-md-2 centrado">
							<svg id="animated" viewbox="0 0 100 100">
							  <circle cx="50" cy="50" r="45" fill="#FFF"/>
							  <path id="progress" stroke-linecap="round" stroke-width="4" stroke="rgb(180,185,210)" fill="none"
							        d="M50 10
							           a 40 40 0 0 1 0 80
							           a 40 40 0 0 1 0 -80">
							  </path>
							  <text id="cronometro" x="50" y="50" text-anchor="middle" dy="7" font-size="11">
							  	00:00
							  </text>
							</svg>	
						</div>
					</div>
					<!-- CRONOMETRO -->
					<!-- MARCADOR PARA LA PREGUNTA ACTUAL-->
					<br>
					<div class="row">
			          	<div class="col-md-10 offset-md-1 centrado">
			          		<table class="table table-sm table-geo" id="tbl-marcador-pregunta" style="display: none">
								<thead>
									<tr>
										<th>Concursante</th>
										<th>Resultado</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
			          	</div>
		          	</div>
					<!-- MARCADOR PARA LA PREGUNTA ACTUAL-->
		        </div>
		        <div class="modal-footer">
		        	<form id="form-lanzar">
		        		<input type="hidden" id="ID_PREGUNTA" name="ID_PREGUNTA">
		        		<input type="hidden" id="ID_GENERADA" name="ID_GENERADA">
		        		<button type="button" class="btn btn-geo" onclick="lanzarPregunta(<?php echo $segundosPorPregunta; ?>,this)" id='btn-lanzar'>
		        			Lanzar pregunta
	        			</button>
	        			<button type="button" class="btn btn-geo" onclick="location.reload();" id="btn-siguiente" style="display: none;">
		        			Siguiente
	        			</button>
		        	</form>
		        </div>
	      	</div>
	    </div>
	</div>
	<!--MODAL LEER-->
	<!-- SCRIPt-->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/snap.svg-min.js"></script>
	<script type="text/javascript" src="js/cronometro.js"></script>
	<script type="text/javascript" src="js/lanzador_preguntas.js"></script>
</body>
</html>