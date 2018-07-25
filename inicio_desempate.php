<!DOCTYPE html>
<html>
<?php 
	require_once 'class/util/Sesion.php';
	require_once 'class/util/SessionKey.php';
	require_once 'class/TableroPuntaje.php';
	$sesion = new Sesion();
	// si el concurso no esta en la ronda 1
	/*if( $sesion->getOne(SessionKey::ID_CONCURSANTE) == null || $sesion->getOne(SessionKey::ID_CONCURSANTE) == null ){
		header('Location: inicio');
	}**/
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
		<div class="card-lg centrado" id="card-inicio">
			<h1 style="color: #545454" class="monserrat-bold"><b>Gracias</b></h1>
			<h4 id="mensaje_concurso">
				<?php echo $sesion->getOne(SessionKey::CONCURSANTE); ?>
				<br><br>
				Han empatado algunos concursantes, espera a que el moderador indique que pueden acceder a la ronda de desempate.
				<br> <br>
				<?php 
					$tablero = new TableroPosiciones();
					$empate = $tablero->esEmpateByConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
					if($empate['estado'] == 1){
						$empatados = $empate['empatados'];
						echo "<table class='table table-sm table-bordered table-geo'>";
						echo "<thead>";
						echo "<tr> <th> # </th>";
						echo "<th> Concursante </th>";
						echo "<th> Puntaje </th> </tr>";
						echo "</thead>";
						foreach ($empatados as $e) {
							echo "<tr>";
							echo "<td>". $e['ID_CONCURSANTE'] . "</td>";
							echo "<td>". $e['CONCURSANTE'] . "</td>";
							echo "<td>". $e['PUNTAJE_TOTAL'] . "</td>";
							echo "</tr>";
						}
						echo "</table>";
					}else{
						echo "NADIE EMPATO";
					}
				?>
				<br>
				<button class="btn btn-geo" onclick="accederDesempate(<?php echo $sesion->getOne(SessionKey::ID_CONCURSO).','.$sesion->getOne(SessionKey::ID_CONCURSANTE); ?>)">
					Continuar
				</button>
			</h4>
		</div>
	</section>
	<!-- INICIO SCRIPTS -->
	<script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/ronda.js"></script>
	<script type="text/javascript" src="js/concursante.js"></script>
	<!-- FIN SCRIPTS  -->
</body>
</html>