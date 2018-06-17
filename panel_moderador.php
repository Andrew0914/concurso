<!DOCTYPE html>
<html>
<?php 
	require_once 'class/Rondas.php';
	require_once 'class/Etapas.php';
	require_once 'class/Categorias.php';
	require_once 'class/Concursante.php';
	require_once 'class/RondasLog.php';
	require_once 'class/PreguntasGeneradas.php';
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA));
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
				<br>
				<button class="btn btn-link btn-sm" style="float: right;" data-toggle="modal" data-target="#mdl-finaliza-ronda">
					Finalizar y cambiar ronda
				</button>
				<br>
			</div>
		</div>
		<hr>
		<!-- TERMINA INFORMACION GENERAL -->
		<!--  GENERACION DE PREGUNTAS -->
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
		<br>
		<!--  GENERACION DE PREGUNTAS -->
		<div class="row">
			<div class="col-md-10 offset-md-1">
				<table  class="table table-sm table-bordered" id="tbl-generadas">
					<thead>
						<tr>
							<th>Categoria</th>
							<th>Preguntas generadas</th>
						</tr>
						<tbody>
							<?php 
								$generadas = new PreguntasGeneradas();
								$generadas = $generadas->getCantidadGeneradas($sesion->getOne(SessionKey::ID_CONCURSO),  $sesion->getOne(SessionKey::ID_RONDA));
								foreach ($generadas as $generada) {
									echo "<tr><td>Geofisica</td><td>".$generada['geofisica']."</td></tr>";
									echo "<tr><td>Geologia</td><td>".$generada['geologia']."</td></tr>";
									echo "<tr><td>Petroleras</td><td>".$generada['petroleros']."</td></tr>";
									echo "<tr><td>Generales</td><td>".$generada['generales']."</td></tr>";
									$total = $generada['geofisica'] + $generada['geologia'] + $generada['petroleros'] + $generada['generales'];
									echo "<tr><td>Total</td><td>".$total."</td></tr>";
								}
							 ?>	
						</tbody>
					</thead>
				</table>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-6">
				<a href="leer_preguntas" target="_blank" class="btn btn-geo" style="width: 50%">
					Inicar ronda
				</a>
			</div>
			<div class="col-md-6">
				<a href="#" target="_blank" class="btn btn-geo" style="width: 50%">
					Finalizar ronda
				</a>
			</div>
		</div>
		<!--  GENERACION DE PREGUNTAS -->
		<hr>
		<!-- INFORMACION CONCURSANTES -->
		<div class="row">
			<div class="col-md-12">
				<b class="monserrat-bold" onclick="$('#tbl-concursantes').slideToggle(500)" style="cursor: pointer;text-decoration: underline;">
					Ver Concursantes
				</b>
				<br>
				<table class="table table-sm" style="display: none" id="tbl-concursantes">
					<thead>
						<tr>
							<th>Concursante</th>
							<th>Password</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$concursantes = new Concursante();
							$concursantes = $concursantes->getConcursantes($sesion->getOne(SessionKey::ID_CONCURSO));
							$concursantes = $concursantes['concursantes'];
							foreach ($concursantes as  $concursante) {
								echo "<tr><td>".$concursante['CONCURSANTE']
									."</td><td>".$concursante['PASSWORD']."</td></tr>";
							}
						 ?>
					</tbody>
				</table>
			</div>	
		</div>
		<!-- INFORMACION CONCURSANTES -->
		<!-- SALIR SESION-->
		<div class="col-md-1 offset-md-11">
			<form method="post" name="form-out"  action="class/util/Sesion.php">
				<input type="hidden" name="functionSesion" id="functionSesion" value="out">
				<button type="submit" class="btn btn-primary btn-sm">
					<b>Salir</b>
				</button>
			</form>
		</div>
		<!-- SALIR SESION-->
		<div class="row">
			<div class="col-md-12 centrado">
				<a href="tablero" class="btn btn-sm btn-link" target="_blank">
					<u>Ver Puntajes</u>
				</a>
			</div>
		</div>
	</div>
	<!-- MODAL FIN RONDA-->
	<div class="modal fade" id="mdl-finaliza-ronda" tabindex="-1" role="dialog" aria-labelledby="mdl-finaliza-rondaLabel" aria-hidden="true">
	  <div class="modal-dialog modal-md" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="mdl-finaliza-rondaLabel">Cambiar y Finalizar Ronda Actual</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      	<select name="RONDA_NUEVA" id="RONDA_NUEVA" class="select-geo">
	      		<option value="">Selecciona la ronda</option>
	      		<?php 
	      			$rondasLog = new RondasLog();
	      			$rondas = $rondasLog->getRondasDisponibles($sesion->getOne(SessionKey::ID_CONCURSO), $sesion->getOne(SessionKey::ID_RONDA), $sesion->getOne(SessionKey::ID_ETAPA))['rondas'];
      				foreach ($rondas as $ronda) {
      					echo "<option value='".$ronda['ID_RONDA']."'>".$ronda['RONDA']."</option>";
      				}
	      		 ?>
	      	</select>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	        <button type="button" class="btn btn-primary" onclick="cambiarFinalizarRonda(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO).",".$sesion->getOne(SessionKey::ID_RONDA); ?>)">
	        	Guardar
	        </button>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- MODAL FIN RONDA-->
	<!-- SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/panel_moderador.js"></script>
</body>
</html>