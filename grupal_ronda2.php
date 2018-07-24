<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/Rondas.php';
	require_once 'class/RondasLog.php';
	require_once 'class/Concurso.php';
	require_once 'class/Categorias.php';
	$sesion = new Sesion();
	// si el concurso no esta en la ronda 1
	if( $sesion->getOne(SessionKey::ID_CONCURSANTE) == null || $sesion->getOne(SessionKey::ID_CONCURSANTE) == null ){
		header('Location: inicio');
	}
	$objConcurso = new Concurso();
	$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
	$ronda = new Rondas();
	$ronda = $ronda->getRonda(5);
	$cat = new Categorias();
	$cat = $cat->getCategoria($concurso['ID_CATEGORIA']);
 ?>
<head>
	<meta charset="utf-8">
	<title>Concursante::Responder</title>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body class="content content-lg azul">
	<section>
		<input type="hidden" id="ID_CONCURSO" name="ID_CONCURSO" value="<?php echo $sesion->getOne(SessionKey::ID_CONCURSO); ?>" />
		<input type="hidden" id="ID_RONDA" name="ID_RONDA" value="<?php echo $ronda['ID_RONDA']; ?>" />
		<input type="hidden" id="ID_CATEGORIA" name="ID_CATEGORIA" value="<?php echo $concurso['ID_CATEGORIA']; ?>" />
		<input type="hidden" id="segundos_ronda" name="segundos_ronda" value="<?php echo $ronda['SEGUNDOS_POR_PREGUNTA']; ?>" />
		<input type="hidden" id="ID_CONCURSANTE" name="ID_CONCURSANTE" value="<?php echo $sesion->getOne(SessionKey::ID_CONCURSANTE); ?>" />
		<input type="hidden" name="IS_DESEMPATE" id="IS_DESEMPATE" value="0">
		<input type="hidden" name="NIVEL_EMPATE" id="NIVEL_EMPATE" value="<?php echo $concurso['NIVEL_EMPATE'] ?>">
		<input type="hidden" name="PREGUNTAS_POR_CATEGORIA" id="PREGUNTAS_POR_CATEGORIA" value="<?php echo $ronda['PREGUNTAS_POR_CATEGORIA'] ?>" />
		<input type="hidden" name="CANTIDAD_PREGUNTAS" id="CANTIDAD_PREGUNTAS" value="<?php echo $ronda['CANTIDAD_PREGUNTAS'] ?>" />
		<div class="card-md centrado" id="card-inicio">
			<h1 style="color: #545454" class="monserrat-bold"><b>Gracias</b></h1>
			<h4 id="mensaje_concurso">
				<?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?>
				<br><br>
				En cuanto el moderador te lance/asigne la pregunta por favor oprime [OBTENER PREGUNTA]
				<br>
				ó
				<br>
				Oprime [OBTENER PREGUNTA DE PASO] si te pasaron una pregunta y el moderador te ha confirmado
				<br>
			</h4>
			<br>
		</div>
		<!-- CRONOMETRO -->
		<div class="row" id="cronometro-content" style="display: none">
			<div class="col-md-4 offset-md-4 centrado">
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
		<article id="pregunta" class="card-lg" style="display: none;">
			<input type="hidden" id="PREGUNTA_POSICION" name="PREGUNTA_POSICION" />
			<input type="hidden" id="ID_PREGUNTA" name="ID_PREGUNTA" />
			<p class="text-pregunta"></p>
			<div class="row">
				<div class="col-md-12 centrado" id="resultado-mi-pregunta">
				</div>
			</div>
			<p id="content-respuestas"></p>
		</article>
		<br>
		<div class="row">
			<div class="col-md-6 centrado">
				<button class="btn btn-geo" onclick="obtenerPregunta()">OBTENER PREGUNTA</button>
			</div>
			<div class="col-md-6 centrado">
				<button class="btn btn-geo" onclick="obtenerPreguntaPaso()">OBTENER PREGUNTA DE PASO</button>
			</div>
		</div>
	</section>
	<!-- PREGUNTAS DE PASO-->
	<div class="modal" id="mdl-pr-paso">
		<div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	<!-- Modal Header -->
		    	<div class="modal-header">
		        	<h4 class="modal-title">Pregunta de paso</h4>
		       		<button type="button" class="close" data-dismiss="modal">&times;</button>
		      	</div>
			    <!-- Modal body -->
			    <div class="modal-body">
			    	<!-- CRONOMETRO PASO-->
					<div class="row" id="cronometro-content-paso" style="display: none">
						<div class="col-md-4 offset-md-4 centrado">
							<svg id="animated-paso" viewbox="0 0 100 100">
							  <circle cx="50" cy="50" r="45" fill="#FFF"/>
							  <path id="progress-paso" stroke-linecap="round" stroke-width="4" stroke="rgb(180,185,210)" fill="none"
									  d="M50 10
										  a 40 40 0 0 1 0 80
										  a 40 40 0 0 1 0 -80">
							  </path>
							  <text id="cronometro-paso" x="50" y="50" text-anchor="middle" dy="7" font-size="11">
								00:00
							  </text>
							</svg>	
						</div>
					</div>
					<!-- CRONOMETRO -->
					<article id="pregunta-paso" class="card-lg" style="display: none;">
						<input type="hidden" id="PREGUNTA_POSICION-paso" name="PREGUNTA_POSICION-paso" />
						<input type="hidden" id="ID_PREGUNTA-paso" name="ID_PREGUNTA-paso" />
						<p class="text-pregunta-paso"></p>
						<div class="row">
							<div class="col-md-12 centrado" id="resultado-mi-pregunta-paso">
							</div>
						</div>
						<p id="content-respuestas-paso"></p>
					</article>
			    </div>
		      	<!-- Modal footer -->
		    	<div class="modal-footer">
		        	<button type="button" class="btn btn-geo" data-dismiss="modal">Cerrar</button>
		      	</div>
		    </div>
	  	</div>
	</div>
	<!-- PREGUNTAS DE PASO-->
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/snap.svg-min.js"></script>
	<script type="text/javascript" src="js/cronometro.js"></script>
	<script type="text/javascript" src="js/mecanica_2nds.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>