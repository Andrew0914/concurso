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
		$sesion = new Sesion();
		$generadas = new PreguntasGeneradas();
		$etapa = new Etapas();
		$etapa = $etapa->getEtapa($sesion->getOne(SessionKey::ID_ETAPA));
		$ronda = new Rondas();
		$ronda = $ronda->getRonda($sesion->getOne(SessionKey::ID_RONDA));
		$segundosPorPregunta = $ronda['SEGUNDOS_POR_PREGUNTA'];
		$idConcurso = $sesion->getOne(SessionKey::ID_CONCURSO);
		$idRonda = $sesion->getOne(SessionKey::ID_RONDA);
		$categoria = new Categorias();
		$categoria= $categoria->getCategoria($sesion->getOne(SessionKey::ID_CATEGORIA));
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
			<!-- RECARGAR PREGUNTAD
			<div class="row">
				<div class="col-md-4 offset-md-8">
					<button class="btn btn-sm btn-link" onclick="location.reload()">
						<u>Recargar preguntas</u>
					</button>
				</div>
			</div>
			<br>
			-->
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
								$preguntas = $generadas->getPreguntasByConcursoRonda($idConcurso,$idRonda,$categoria['ID_CATEGORIA']);
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
				<div class="row" style="width: 100%">
					<div class="col-md-5 offset-md-7 centrado">
						<button class="btn btn-geo btn-block" onclick="siguienteRonda()">
							Siguiente ->
						</button>
					</div>
				</div>
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
		    				<div class="col-md-4 offset-md-8">
		    					<h3 id="titulo_modal" class="modal-title monserrat-bold" style="float: right;">
						          	Leer pregunta
						        </h3>
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
							<div class="col-md-7">
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
		</style>
		<!-- SCRIPt-->
		<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/libs/bootstrap.js"></script>
		<script type="text/javascript" src="js/snap.svg-min.js"></script>
		<script type="text/javascript" src="js/cronometro.js"></script>
		<script type="text/javascript" src="js/ronda.js"></script>
		<script type="text/javascript" src="js/lanzador_preguntas.js"></script>
		<?php 
			if ($todasHechas) {
		 ?>
		<script type="text/javascript">
			window.scrollTo(0,document.body.scrollHeight);
		</script>
		<?php } ?>
	</body>
</html>