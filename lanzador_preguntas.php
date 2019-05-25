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
		require_once 'class/Categorias.php';
		require_once 'class/TableroPuntaje.php';
		require_once 'class/TableroMaster.php';

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
		<link rel="stylesheet" type="text/css" href="css/libs/bootstrap-reboot.min.css">
		<link rel="stylesheet" type="text/css" href="css/libs/fontawesome/css/all.css">
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
					<b class="monserrat-bold">Categoria elegida:</b> <?php echo $categoria['CATEGORIA']; ?>
					<br>
					<b class="monserrat-bold">Ronda elegida:</b> <?php echo $ronda['RONDA']; ?>
				</div>
			</div>
			<hr>
			<!-- INFORMACION GENERAL-->
			<!-- PREGUNTAS GENERADAS-->
			<div class="row">
				<div class="col-md-12">
					<table class="table table-sm table-bordered table-striped table-geo">
						<thead>
							<tr>
								<th>#Pregunta</th>
								<th>Pregunta</th>
								<th>Dificultad</th>
								<th>Puntaje</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$preguntas = $generadas->getPreguntasByConcursoRonda($idConcurso,$idRonda,$categoria['ID_CATEGORIA'],$ronda['IS_DESEMPATE'],$concurso['NIVEL_EMPATE']);
								$onclick = "";
								foreach ($preguntas as $pregunta) {
									echo "<tr class='monserrat-bold'>";
									echo "<td>" . $pregunta['PREGUNTA_POSICION']. "</td>";
									echo "<td>" . $pregunta['CATEGORIA']. "</td>";
									echo  "<td>".$pregunta['DIFICULTAD'] ."</td>";
									echo "<td>". $pregunta['PUNTAJE']."</td>";
									$onclick = " onclick='leer(\"".addslashes ($pregunta['PREGUNTA'])."\",";
									$onclick .= $pregunta['ID_PREGUNTA']. ",";
									$onclick .= $pregunta['PUNTAJE']. ",";
									$onclick .= $pregunta['ID_GENERADA'].")'"; 
									$button = "<td class='centrado'><button class='btn-geo'".$onclick.">Leer</button></td>";
									if($pregunta['HECHA'] == 1){
										$button= "<td class='centrado'><button class='btn btn-sm btn-dark'>HECHA</button></td>";
									}
									echo $button;
									echo  "</tr>";
								}
							 ?>
						</tbody>
					</table>
				</div>
				<?php 
					$todasHechas = $generadas->todasHechas($idConcurso,$idRonda,$categoria['ID_CATEGORIA']);
					if($todasHechas){
				?> 
				<!-- CAMBIO DE RONDA -->
				<div class="row" style="width: 100%">
					<div class="col-md-5 offset-md-7 centrado">
						<button class="btn btn-geo btn-block" onclick="siguienteRonda()">
							<i class="fas fa-forward fa-2x"></i>
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
					        	<h4 class="modal-title">Puntajes de la Ronda</h4>
					       		<button type="button" class="close" data-dismiss="modal">&times;</button>
					      	</div>
						    <!-- Modal body -->
						    <div class="modal-body">
						    	<div id="divmejores">
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
												$response = $tablero->getMejoresRonda($concurso['ID_CONCURSO'],$concurso['ID_RONDA'],$concurso['NIVEL_EMPATE']);
												$mejores = $response['mejores'];
												foreach ($mejores as $mejor) {
													echo "<tr>";
													echo "<td><b>".$mejor['lugar']."<small> lugar</small></b></td>";
													echo "<td>".$mejor['CONCURSANTE'] . '</td>';
													echo "<td>".$mejor['totalPuntos'].'</td>';
													echo "</tr>";	
												}
											 ?>
										</tbody>
									</table>
								</div>
								<br>
								<button class="btn btn-lg btn-geo" onclick="mostrarResumen()">
									Puntuaciones
								</button>
								<br>
								<!--PUNTAJES DETALLE PRELIMINARES -->
								<div id="divtablero" style="display: none;">
									<table class="table table-bordered table-geo" id="tbl-puntaje" style="width: 100%;">
										<thead>
											<tr>
												<th>Concursante</th>
												<th>Ronda</th>
												<th>Categoria</th>
												<th> Pregunta </th>
												<th>Respuesta del concursante</th>
												<th>Puntaje</th>
												<th>Roba Puntos</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$puntajes = $tablero->getResultados($concurso['ID_CONCURSO'],$ronda['IS_DESEMPATE'],true)['tablero'];
												foreach ($puntajes as $puntaje) {
													echo "<tr>";
													echo "<td>" . $puntaje['CONCURSANTE'] . '</td>';
													echo "<td>" . $puntaje['RONDA'] . '</td>';
													echo "<td>" . $puntaje['CATEGORIA'] .'</td>';
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
													echo "<td>".$puntaje['PASO_PREGUNTAS'].'</td>';
													echo "</tr>";	
												}
											?>
										</tbody>
									</table>
								</div>
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
		    				<div class="col-md-5 offset-md-7 text-right">
								<div class="row">
									<div class="col-10">
										<h3 id="titulo_modal" class="info-remark modal-title monserrat-bold" >
											Leer pregunta
										</h3>
									</div>
									<div class="col-2">
										<button class='btn btn-secondary btn-sm' data-dismiss="modal">
											<i class='fas fa-times'></i>
										</button>
									</div>
								</div>
		    				</div>
		    			</div>
				    </div>
			        <div class="modal-body">
			        	<!-- CRONOMETRO -->
						<div class="row" id="cronometro-content" style="display: none">
							<!-- HISTOGRAMA -->
							<div class="col-md-5" id="histograma">
								<div class="row">
									<div class="col-md-1" id="num_correctas"></div>
									<div class="col-md-10">
										<div class="barra-histograma" id="histo-correctas">correctas</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-1" id="num_incorrectas"></div>
									<div class="col-md-10">
										<div class="barra-histograma" id="histo-incorrectas">incorrectas</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-1" id="num_contestadas"></div>
									<div class="col-md-10">
										<div class="barra-histograma" id="histo-contestadas">contestaron</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-1" id="num_nocontestadas"></div>
									<div class="col-md-10">
										<div class="barra-histograma" id="histo-nocontestadas">no contestaron</div>
									</div>
								</div>
							</div>
							<!-- HISTOGRAMA -->
							<div class="col-md-7" id="reloj-cronometro">
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
						<!-- PREGUNTA-->
			         	<div class="row">
				          	<div class="col-md-12">
				          		<br>
				          		<h2 id="p-pregunta" class="monserrat-bold centrado"></h2>
				          	</div>
			          	</div>
			          	<!-- PREGUNTA-->
			          	<!-- RESPUESTAS -->
			          	<br>
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
			        		<button type="button" class="btn btn-lg btn-geo" onclick="lanzarPregunta(<?php echo $segundosPorPregunta; ?>,this)" id='btn-lanzar'>
			        			Lanzar pregunta
		        			</button>
		        			<button type="button" class="btn btn-lg btn-geo" onclick="location.reload();" id="btn-siguiente" style="display: none;">
								<i class="fas fa-forward"></i>
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

			.tooltip-inner,.tooltip {
				max-width: 500px;
				width: 500px;
				max-height: 150px;
				height: auto;
				font-size: 18px;
				text-align: justify;
			}
		</style>
		<!-- SCRIPt-->
		<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/libs/popper.min.js"></script>
		<script type="text/javascript" src="js/libs/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/libs/snap.svg-min.js"></script>
		<script type="text/javascript" src="js/cronometro.js"></script>
		<script type="text/javascript" src="js/ronda.js"></script>
		<script type="text/javascript" src="js/lanzador_preguntas.js"></script>
		<script type="text/javascript">
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