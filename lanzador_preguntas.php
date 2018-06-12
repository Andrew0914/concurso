<!DOCTYPE html>
<html>
<!--PROCESAMIENTO-->
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/PreguntasGeneradas.php';
	require_once 'class/Rondas.php';
	require_once 'class/Etapas.php';
	$sesion = new Sesion();
	$generadas = new PreguntasGeneradas();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA))
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
			<div class="col-md-2 offset-md-10">
				<button class="btn btn-sm btn-link" onclick="location.reload()" style="text-decoration: underline;">
					Recargar preguntas
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
							$preguntas = $generadas->getPreguntasByConcursoRonda($sesion->getOne(SessionKey::ID_CONCURSO),$sesion->getOne(SessionKey::ID_RONDA));
							$onclick = "";
							foreach ($preguntas as $pregunta) {
								echo "<tr>";
								echo "<td>" . $pregunta['PREGUNTA_POSICION']. "</td>";
								echo "<td>" . $pregunta['PREGUNTA']. "</td>";
								$onclick = " onclick='leer(\"".$pregunta['PREGUNTA']."\",";
								$onclick .= $pregunta['ID_PREGUNTA']. ",";
								$onclick .= $pregunta['ID_GENERADA'].")'"; 
								echo "<td><button class='btn-geo'".$onclick.">Leer</button></td>";
								echo  "</tr>";
							}
						 ?>
					</tbody>
				</table>
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
		          	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	</div>
		        <div class="modal-body">
		          <div class="row">
		          	<div class="col-md-12">
		          		<h4 id="p-pregunta" class="monserrat-bold centrado"></h4>
		          	</div>
		          </div>
		        </div>
		        <div class="modal-footer">
		          <button type="button" class="btn btn-geo" data-dismiss="modal">Lanzar pregunta</button>
		        </div>
	      	</div>
	    </div>
	</div>
	<!--MODAL LEER-->
	<!-- SCRIPt-->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/lanzador_preguntas.js"></script>
</body>
</html>