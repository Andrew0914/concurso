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
	require_once 'class/TableroMaster.php';
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
	<link rel="stylesheet" type="text/css" href="css/libs/fontawesome/css/all.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="blanco content content-md">
	<input type="hidden" value="<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>"  id="ID_CONCURSO"/>
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
				<!-- SALIR SESION-->
				<form method="post" name="form-out"  action="class/util/Sesion.php" class="mt-1">
					<input type="hidden" name="functionSesion" id="functionSesion" value="out">
					<button type="submit" class="btn btn-primary btn-sm">
						Salir <i class="fas fa-sign-out-alt"></i>
					</button>
				</form>
				<!-- SALIR SESION-->
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
				<?php 
					// NO CERRADOS
					if($concurso['FECHA_CIERRE'] == '' || $concurso['FECHA_CIERRE'] == null){
				?>
				<button class="btn btn-block btn-geo" onclick="generaTableros(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>)">
					<h5 class="monserrat-bold">Calcular Puntajes <i class="fas fa-trophy"></i></h5>
				</button>
				<br><br>
				<i class="fa fa-spinner fa-pulse fa-3x fa-fw" id="loading-s" style="display: none"></i>
				<?php } ?>
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
				<div class="row">
					<div class="col">
						<b class="monserrat-bold">
							Concursantes
						</b>
					</div>
					<div class="col text-right">
						<button class="btn btn-sm btn-secondary" onclick="$('.td-password').slideToggle(500)">Mostrar passwords</button>
						<button class="btn btn-sm btn-info" onclick="<?php echo 'fetchConcursantes(' . $sesion->getOne(SessionKey::ID_CONCURSO) . ')'?>">
							<i class="fas fa-sync-alt"></i>
						</button>
					</div>
				</div>
				
				<br>
				<table class="table table-sm"  id="tbl-concursantes">
					<thead>
						<tr>
							<th>Concursante</th>
							<th style='display:none' class='td-password'>Password</th>
							<th>Sesión Iniciada</th>
							<th>¿No puede entrar?</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>	
		</div>
		<!-- INFORMACION CONCURSANTES -->

	</div>
	<!-- SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<script type="text/javascript" src="js/panel_moderador.js"></script>
</body>
</html>