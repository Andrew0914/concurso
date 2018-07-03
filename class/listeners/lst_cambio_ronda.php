<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$rondaActual = $_GET['rondaActual'];
		$categoriaActual = $_GET['categoriaActual'];
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		// Mientras no haya cambiado la ronda
		while($concurso['ID_RONDA'] == $rondaActual AND $concurso['ID_CATEGORIA'] == $categoriaActual) {
			usleep(20000);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		}

		$cambio = ['estado'=>1,
					'mensaje'=>'Cambio de ronda',
					'ronda'=>$concurso['ID_RONDA'],
					'categoria'=>$concurso['ID_CATEGORIA']];
					
		echo json_encode($cambio);
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}
?>