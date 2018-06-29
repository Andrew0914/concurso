<!DOCTYPE html>
<html>
<?php 
	require_once 'class/Etapas.php';
	require_once 'class/Categorias.php';
	require_once 'class/Concursante.php';
	require_once 'class/PreguntasGeneradas.php';
	require_once 'class/RondasLog.php';
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	$sesion = new Sesion();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$categoria = new Categorias();
	$categoria = $categoria->getCategoria($sesion->getOne(SessionKey::ID_CATEGORIA));
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
		<div class="row" >
			<div class="col-md-6 sm-text">
				<b class="monserrat-bold">Concurso:</b> <?php echo $sesion->getOne(SessionKey::CONCURSO); ?>
				<br>
				<b class="monserrat-bold">Etapa:</b> <?php echo $etapa['ETAPA']; ?>
			</div>
			<div class="col-md-6 sm-text">
				<b class="monserrat-bold">Categoria:</b> <?php echo $categoria['CATEGORIA']; ?>
			</div>
		</div>
		<hr>
		<!-- TERMINA INFORMACION GENERAL -->
		<!-- PUNTAJES SI ES EL CASO-->
		<?php 
			$log = new RondasLog();
			if($log->rondasTerminadas($sesion->getOne(SessionKey::ID_CONCURSO))){
		?>
		<br>
		<div class="row">
			<div class="col-md-12 centrado">
				<a href="tablero" class="btn btn-block btn-geo" target="_self">
					<h5 class="monserrat-bold">Ver Puntajes</h5>
				</a>
			</div>
		</div>
		<br>
		<?php } ?>
		<!-- PUNTAJES SI ES EL CASO-->
		<!--  GENERACION DE PREGUNTAS -->
		<div class="row">
			<div class="col-md-8">
				<b class="monserrat-bold">Generar Preguntas</b>
				<select class="select-geo" style="margin: 0;width: 100%" id="ID_CATEGORIA">
					<option value="">Selecciona categoria</option>
					<?php 
						$categoria = new Categorias();
						$categorias = $categoria->getCategoriasPermitidas($sesion->getOne(SessionKey::ID_ETAPA))['categorias'];
						foreach ($categorias as $cat) {
							echo "<option value='".$cat['ID_CATEGORIA']."'>" .$cat['CATEGORIA']. "</option>";
						}
					 ?>
				</select>
			</div>
			<div class="col-md-4">
				<br>
				<button class="btn-geo" onclick="generaPreguntas(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO).','.$sesion->getOne(SessionKey::ID_ETAPA); ?>)">
					Generar
				</button>
			</div>
		</div>
		<br>
		<!--  GENERACION DE PREGUNTAS -->
		<div class="row">
			<div class="col-md-12">
				<table  class="table table-sm table-bordered table-striped" id="tbl-generadas">
					<thead>
						<tr>
							<th>Rondas</th>
							<th>Geogísica</th>
							<th>Geolofía</th>
							<th>Petroleros</th>
							<th>Generales</th>
						</tr>
					</thead>
						<tbody>
							<?php 
								$generadas = new PreguntasGeneradas();
								$generadas = $generadas->getCantidadGeneradas($sesion->getOne(SessionKey::ID_ETAPA),  
									$sesion->getOne(SessionKey::ID_CONCURSO));
								$contadores = $generadas['contadores'];
								for ($i=0; $i < count($contadores) ; $i++) { 
									echo "<tr>";
									echo "<td>". $contadores[$i]['ronda'] . "</td>";
									echo "<td>". $contadores[$i]['geofisica'] . "</td>";
									echo "<td>". $contadores[$i]['geologia'] . "</td>";
									echo "<td>". $contadores[$i]['petroleros'] . "</td>";
									echo "<td>". $contadores[$i]['generales'] . "</td>";
									echo "</tr>";
								}
							 ?>
						</tbody>
				</table>
				<table  class="table table-sm table-bordered">
					<tr>
					 	<td>
					 		Elige una categoria a iniciar:&nbsp;&nbsp;&nbsp;&nbsp;

					 	</td>
					 	<td>
					 		<button class="btn btn-sm btn-geo" onclick="iniciarCategoria(1,<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					 			Iniciar
					 		</button>
					 	</td>
					 	<td>
					 		<button class="btn btn-sm btn-geo" onclick="iniciarCategoria(2,<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					 			Iniciar
					 		</button>
					 	</td>
					 	<td>
					 		<button class="btn btn-sm btn-geo" onclick="iniciarCategoria(3,<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					 			Iniciar
					 		</button>
					 	</td>
					 	<td>
					 		<button class="btn btn-sm btn-geo" onclick="iniciarCategoria(4,<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					 			Iniciar
					 		</button>
					 	</td>
					</tr>
				</table>
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
	</div>
	<!-- SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<script type="text/javascript" src="js/panel_moderador.js"></script>
</body>
</html>