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
		require_once 'class/TableroPaso.php';
		require_once 'class/TableroPuntaje.php';
		require_once 'class/Concursante.php';
		$sesion = new Sesion();
		$generadas = new PreguntasGeneradas();
		$etapa = new Etapas();
		$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
		$ronda = new Rondas();
		$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA));
		$segundosPorPregunta = $ronda['SEGUNDOS_POR_PREGUNTA'];
		$idConcurso = $sesion->getOne(SessionKey::ID_CONCURSO);
		$concurso = new Concurso();
		$concurso = $concurso->getConcurso($idConcurso);
		$idRonda = $sesion->getOne(SessionKey::ID_RONDA);
		$categoria = new Categorias();
		$categoria= $categoria->getCategoria($sesion->getOne(SessionKey::ID_CATEGORIA));
		$tablero = new TableroPuntaje();
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
			<input type="hidden" id="ID_CATEGORIA" name="ID_CATEGORIA" value="<?php echo $sesion->getOne(SessionKey::ID_CATEGORIA); ?>">
			<input type="hidden" name="IS_DESEMPATE" id="IS_DESEMPATE" value="<?php echo $ronda['IS_DESEMPATE'] ?>">
			<input type="hidden" name="NIVEL_EMPATE" id="NIVEL_EMPATE" value="<?php echo $concurso['NIVEL_EMPATE'] ?>">
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
					<br>
					<b class="monserrat-bold">Ronda elegida:</b> <?php echo $ronda['RONDA']; ?>
				</div>
			</div>
			<hr>
			<!-- INFORMACION GENERAL-->
			<!-- PREGUNTAS GENERADAS-->
			<div class="row">
				<div class="col-md-12">
					<table class='table table-sm table-bordered table-striped table-geo'>
						<thead>
							<tr>
								<th>Concursante</th>
								<th>Categoria</th>
								<th>Puntaje</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$preguntas = $generadas->getPreguntas2nda($idConcurso,$idRonda);
								$onclick = "";
								$concursante = new Concursante();
								$countConcursantes = $concursante->getCountConcursates($sesion->getOne(SessionKey::ID_CONCURSO))[0]['total'];
								$prMostradas = 1;
								$cambio = "";
								foreach ($preguntas as $pregunta) {
									if($prMostradas == $countConcursantes){
										$cambio = " style='border-bottom:20px solid rgb(140,138,130)' ";
										$prMostradas = 0;
									}else{
										$cambio = "";
									}
									echo "<tr class='monserrat-bold'". $cambio .">";
									echo "<td>" . $pregunta['CONCURSANTE']. "</td>";
									echo "<td>" . $pregunta['CATEGORIA']. "</td>";
									echo "<td>". $pregunta['PUNTAJE']."</td>";
									$onclick = " onclick='leer(\"".addslashes ($pregunta['PREGUNTA'])."\",";
									$onclick .= $pregunta['ID_PREGUNTA']. ",";
									$onclick .= $pregunta['PUNTAJE']. ",";
									$onclick .= "\"".$pregunta['CONCURSANTE']. "\",";
									$onclick .= $pregunta['ID_CONCURSANTE']. ",";
									$onclick .= $pregunta['ID_GENERADA'].")'"; 
									$button = "<td class='centrado'><button class='btn-geo'".$onclick.">Leer</button></td>";
									if($pregunta['HECHA'] == 1){
										$button= "<td class='centrado'><button class='btn btn-sm btn-dark'>HECHA</button></td>";
									}
									echo $button;
									echo  "</tr>";
									$prMostradas++;
								}
							 ?>
						 </tbody>
					</table>
				</div>
				<?php 
					$todasHechas = $generadas->todasHechas($idConcurso,$idRonda,null,true);
					if($todasHechas){
				?> 
				<!-- CAMBIO DE RONDA -->
				<div class="row" style="width: 100%">
					<div class="col-md-5 offset-md-7 centrado">
						<button class="btn btn-geo btn-block" onclick="siguienteRonda()">
							Siguiente ->
						</button>
					</div>
				</div>
				<!-- FIN CAMBIO DE RONDA -->
				<!-- TABLERO PRELIMINAR  POR RONDA-->
				<div class="modal" id="mdl-preliminar">
					<div class="modal-dialog modal-big">
					    <div class="modal-content">
					    	<!-- Modal Header -->
					    	<div class="modal-header">
					        	<h4 class="modal-title">Puntajes preliminares</h4>
					       		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      	</div>
						    <!-- Modal body -->
						    <div class="modal-body">
						    	<div id="divmejores">
						    		<!--PREGUNTAS NORMALES-->
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
												$response = $tablero->getMejoresPuntajes($sesion->getOne(SessionKey::ID_CONCURSO),$ronda['IS_DESEMPATE'] );
												$mejores = $response['mejores'];
												// obtengo los puntajes de paso tambien
												$pasos = new TableroPaso();
												$mejoresPaso = $pasos->getMejores($concurso)['mejores'];
												// unifico/sumo puntajes de los ordinarios y robapuntos
												for($x = 0 ; $x<count($mejores) ; $x++) {
													foreach ($mejoresPaso as $mp) {
														if($mejores[$x]['ID_CONCURSANTE'] == $mp['ID_CONCURSANTE']){
															$mejores[$x]['totalPuntos'] = $mejores[$x]['totalPuntos']  + $mp['totalPuntos'];
														}
													}
												}
												// muestro puntuaciones totales en una tabla
												foreach ($mejores as $mejor) {
													echo "<tr>";
													echo "<td>".$mejor['lugar']."<small> lugar</small></td>";
													echo "<td>".$mejor['CONCURSANTE'] . '</td>';
													echo "<td>".$mejor['totalPuntos'].'</td>';
													echo "</tr>";	
												}
											 ?>
										</tbody>
									</table>
									<!--PUNTAJES DETALLE PRELIMINARES -->
									<table class="table table-bordered table-geo" id="tbl-puntaje" style="width: 100%;">
										<thead>
											<tr>
												<th>Concursante</th>
												<th>Ronda</th>
												<th>Categoria</th>
												<th> Pregunta </th>
												<th>Respuesta</th>
												<th>Puntaje</th>
												<th>Roba Puntos</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$puntajes = $tablero->getResultados($concurso['ID_CONCURSO'],$ronda['IS_DESEMPATE'])['tablero'];
												foreach ($puntajes as $puntaje) {
													echo "<tr>";
													echo "<td>" . $puntaje['CONCURSANTE'] . '</td>';
													echo "<td>" . $puntaje['RONDA'] . '</td>';
													echo "<td>" . $puntaje['CATEGORIA'] .'</td>';
													echo "<td>".$puntaje['PREGUNTA'].'</td>';
													echo "<td><b>".$puntaje['INCISO'].')&nbsp;</b>';
													if($puntaje['ES_IMAGEN'] == 1){
														echo '<img src="image/respuestas/'.$puntaje['RESPUESTA'].'"></td>';;
													}else{	
														echo $puntaje['RESPUESTA'].'</td>';
													}
													echo "<td>".$puntaje['PUNTAJE'].'</td>';
													echo "<td>".$puntaje['PASO_PREGUNTAS'].'</td>';
													echo "</tr>";	
												}
											 ?>
										</tbody>
									</table>
								</div>
						    </div>
					      	<!-- Modal footer -->
					    	<div class="modal-footer">
					        	<button type="button" class="btn btn-geo" data-dismiss="modal">Cerrar</button>
					      	</div>
					    </div>
				  	</div>
				</div>
				<!-- FIN TABLERO PRELIMINAR POR RONDA-->
				<?php } ?>
			</div>
			<!-- PREGUNTAS GENERADAS-->
		</section>
		<!--MODAL LEER-->
		<div id="mdl-leer-pregunta" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-full" role="document">
		    	<div class="modal-content blanco">
		    		<div class="modal-header">
		    			<div class="row" style="width:100%">
		    				<div class="col-md-6">
		    					<h3 id="concursante" class="modal-title monserrat-bold" style="float: left;background: white">
						          	Equipo
						        </h3>
		    				</div>
		    				<div class="col-md-6">
		    					<h3 id="titulo_modal" class="modal-title monserrat-bold" style="float: right;">
						          	Leer pregunta
						        </h3>
		    				</div>
		    			</div>
				    </div>
			        <div class="modal-body">
			        	<!-- CRONOMETRO -->
						<div class="row" id="cronometro-content">
							<div class="col-md-12 centrado">
								<img src="image/loading.gif" id="loading" style="width:8%;display: none;" />	
							</div>
						</div>
						<!-- CRONOMETRO -->
						<!-- PREGUNTA-->
			         	<div class="row">
				          	<div class="col-md-12">
				          		<br><br>
				          		<h2 id="p-pregunta" class="monserrat-bold centrado"></h2>
				          	</div>
			          	</div>
			          	<!-- PREGUNTA-->
			          	<!-- RESPUESTAS -->
			          	<br><br>
			          	<div class="row">
			          		<div class="col-md-12">
			          			<table class="table table-bordered table-geo" id="content-respuestas">
			          				<tbody class="monserrat-bold"></tbody>
			          			</table>
			          		</div>
			          	</div>
			          	<!-- RESPUESTAS -->
			        </div>
			        <div class="modal-footer">
			        	<form id="form-lanzar">
			        		<input type="hidden" id="ID_PREGUNTA" name="ID_PREGUNTA">
			        		<input type="hidden" id="ID_GENERADA" name="ID_GENERADA">
			        		<input type="hidden" id="ID_CONCURSANTE" name="ID_CONCURSANTE">
			        		<button type="button" class="btn btn-lg btn-geo" onclick="lanzarPregunta(<?php echo $segundosPorPregunta; ?>,this)" id='btn-lanzar'>
			        			Lanzar pregunta
		        			</button>
		        			<button type="button" class="btn btn-lg btn-geo" onclick="location.reload();" id="btn-siguiente" style="display: none;">
			        			Siguiente
		        			</button>
			        	</form>
			        </div>
		      	</div>
		    </div>
		</div>
		<!--MODAL LEER-->
		<style type="text/css">
			.modal-full {
			    min-width: 100%;
			    margin: 0;
			}

			.modal-full .modal-content {
			    min-height: 100vh;
			}
			.modal-big {
			    min-width: 95%;
			}

			.modal-big .modal-content {
			    min-height: 100vh;
			}
		</style>
		<!-- SCRIPt-->
		<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/libs/bootstrap.js"></script>
		<script type="text/javascript" src="js/snap.svg-min.js"></script>
		<script type="text/javascript" src="js/cronometro.js"></script>
		<script type="text/javascript" src="js/ronda.js"></script>
		<script type="text/javascript" src="js/lanzador_2nda.js"></script>
		<?php 
			if ($todasHechas) {
		 ?>
		<script type="text/javascript">
			window.scrollTo(0,document.body.scrollHeight);
			$("#mdl-preliminar").modal();
		</script>
		<?php } ?>
	</body>
</html>