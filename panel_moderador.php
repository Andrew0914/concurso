<!DOCTYPE html>
<html>
<?php 
	require_once 'class/Etapas.php';
	require_once 'class/Categorias.php';
	require_once 'class/Concursante.php';
	require_once 'class/PreguntasGeneradas.php';
	require_once 'class/RondasLog.php';
	require_once 'class/Rondas.php';
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Concurso.php';
	$sesion = new Sesion();
	$etapa = new Etapas();
	$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
	$objCategoria = new Categorias();
	$categoria = $objCategoria->getCategoria($sesion->getOne(SessionKey::ID_CATEGORIA));
	$concurso = new Concurso();
	$concurso = $concurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda($concurso['ID_RONDA']);

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
				<input type="hidden" name="IS_DESEMPATE" id="IS_DESEMPATE" value="<?php echo $ronda['IS_DESEMPATE'] ?>">
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
			$concurso1 = new Concurso();
			$log = new RondasLog();
			$rondasTerminadas = $log->rondasTerminadasCategoria($sesion->getOne(SessionKey::ID_CONCURSO) , $categoria['ID_CATEGORIA']);
			$concursoCerrado = $concurso1->concursoCerrado($sesion->getOne(SessionKey::ID_CONCURSO));
			if($rondasTerminadas AND true){
		?>
		<br>
		<div class="row">
			<div class="col-md-12 centrado">
				<button class="btn btn-block btn-geo" onclick="generaTableros(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					<h5 class="monserrat-bold">Calcular y Ver Puntajes</h5>
				</button>
				<br><br>
				<img src="image/loading.gif" width="50" height="50" id="loading-s" style="display: none" /> 
			</div>
		</div>
		<br>
		<?php } ?>
		<!-- PUNTAJES SI ES EL CASO-->
		<div class="row">
			<div class="col-md-12">
				<table  class="table table-sm table-bordered table-striped" id="tbl-generadas">
					<thead>
						<tr>
							<th>Rondas</th>
							<th> <?php echo $categoria['CATEGORIA']; ?> </th>
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
									switch ($categoria['ID_CATEGORIA']) {
										case 1:
											echo "<td>". $contadores[$i]['geofisica'] . "</td>";
										break;
										case 2:
											echo "<td>". $contadores[$i]['geologia'] . "</td>";
										break;
										case 3:
											echo "<td>". $contadores[$i]['petroleros'] . "</td>";
										break;
										case 4:
											echo "<td>". $contadores[$i]['generales'] . "</td>";
										break;
									}
									echo "</tr>";
								}
							 ?>
						</tbody>
				</table>
				<div class="row">
					<div class="col-md-4 offset-md-8">
						<?php if(!$rondasTerminadas){ ?>
						<button class="btn btn-block btn-geo" onclick="iniciarCategoria(<?php echo $categoria['ID_CATEGORIA'].','.$sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					 			Iniciar
					 	</button>
					 	<?php } 
					 		if($concurso1->concursoCerrado($sesion->getOne(SessionKey::ID_CONCURSO))){
					 	?>
					 	<!--<button class="btn btn-block btn-geo">
					 		Revisar puntuaciones
					 	</button>-->
					 	<?php } ?>
					</div>
				</div>
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