<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$rondaActual = $_GET['rondaActual'];
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		// Mientras no haya cambiado la ronda
		while ($concurso['ID_RONDA'] == $rondaActual ) {
			usleep(10000);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		}
		$cambio = ['estado'=>1,
					'mensaje'=>'Cambio de ronda',
					'ronda'=>$concurso['ID_RONDA']];
					
		echo json_encode($cambio);
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}
?>