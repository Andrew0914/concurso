<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Concurso.php';
	require_once 'class/TableroPuntaje.php';
	$sesion = new Sesion();
	$tablero = new TableroPuntaje();
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
			<div class="col-md-4 offset-md-8">
				<button class="btn btn-geo btn-sm" style="float: right;" data-toggle="modal" data-target="#mdl-finaliza-ronda">
					Finalizar y cambiar ronda
				</button>
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
							<?php 
								$response = $tablero->getTableroDisplay($sesion->getOne(SessionKey::ID_CONCURSO),$sesion->getOne(SessionKey::ID_RONDA));
								$tableros = $response['tablero'];
								foreach ($tableros as $tab) {
									echo "<tr>";
									echo "<td>" . $tab['CONCURSANTE'] . '</td>';
									echo "<td>".$tab['PREGUNTA_POSICION'].'</td>';
									echo "<td><b>".$tab['INCISO'].')&nbsp;</b>';
									if($tab['ES_IMAGEN'] == 1){
										echo '<img src="image/respuestas/'.$tab['RESPUESTA'].'"></td>';;
									}else{	
										echo $tab['RESPUESTA'].'</td>';
									}
									echo "<td>".$tab['PASO_PREGUNTA'].'</td>';
									echo "<td>".$tab['PUNTAJE'].'</td>';
									echo "</tr>";	
								}
							 ?>
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
							<?php 
								$response = $tablero->getMejoresPuntajes($sesion->getOne(SessionKey::ID_CONCURSO),$sesion->getOne(SessionKey::ID_RONDA));
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
			</div>
		</div>
		<!--/TABLERO PUNTAJE -->
		<br>
		<button class="btn btn-geo" id="btnObtenerPuntaje" onclick="location.reload();" >
			Actualizar tablero
		</button>
	</section>
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
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/libs/bootstrap.js"></script>
	<script type="text/javascript" src="js/concurso.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>