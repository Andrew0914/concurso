<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Concurso.php';
	$sesion = new Sesion();
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
		<div class="row">
			<div class="col-md-1 offset-md-11">
				<form method="post" name="form-out"  action="class/util/Sesion.php">
					<input type="hidden" name="functionSesion" id="functionSesion" value="out">
					<button type="submit" class="btn btn-primary btn-sm">
						<b>Salir</b>
					</button>
				</form>
			</div>
		</div>
		<h1 class="title">
			Concurso: <?php  echo $sesion->getOne(SessionKey::CONCURSO); ?>
		</h1>
		<h4 class="title"> 
			<b>Etapa:</b> <?php  echo $sesion->getOne(SessionKey::ETAPA); ?> &nbsp;&nbsp; 
			<b>Ronda:</b> <?php  echo $sesion->getOne(SessionKey::RONDA); ?>	
		</h4>
		<br>
		<!-- TABLERO PUNTAJE -->
		<div class="row">
			<div class="col-md-7">
				<div style="max-height: 360px;overflow-y: scroll;" id="divtablero">
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
							<tr></tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-5">
				<div style="max-height: 360px;overflow-y: scroll;" id="divmejores">
					<table class="table table-bordered table-geo" id="tbl-mejores" style="width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th>Concursante</th>
								<th>Puntaje Total</th>
							</tr>
						</thead>
						<tbody>
							<tr></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!--/TABLERO PUNTAJE -->
		<!--INICIO DEL CONCURSO-->
		<?php 
			$objConcurso = new Concurso();
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
			if(!$concurso['INICIO_CONCURSO']){
		?>
		<form id="form-iniciar-concurso">
			<input type="hidden" name="ID_CONCURSO" id="ID_CONCURSO" value="<?php  echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>"/>
			<button type='button' class="btn btn-lg btn-geo" onclick="iniciarConcurso($('#form-iniciar-concurso'),this)">
				Iniciar concurso
			</button>
		</form>
		<?php } ?>
		<!--INICIO DEL CONCURSO-->
		<br>
		<button class="btn btn-geo" id="btnObtenerPuntaje" onclick="obtenerPuntaje(<?php  echo $sesion->getOne(SessionKey::ID_CONCURSO) . "," . $sesion->getOne(SessionKey::ID_RONDA); ?>)" <?php echo $concurso['INICIO_CONCURSO']==1 ? "":" style='display:none'"  ?>>
			Obtener Puntajes
		</button>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>