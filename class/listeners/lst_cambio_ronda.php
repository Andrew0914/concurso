<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	require_once dirname(__FILE__) . '/../RondasLog.php';
	require_once dirname(__FILE__) . '/../TableroPuntaje.php';

	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$rondaActual = $_GET['rondaActual'];
		$categoriaActual = $_GET['categoriaActual'];
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		$log = new RondasLog();
		$termino = 0;
		// Mientras no haya cambiado la ronda
		while($concurso['ID_RONDA'] == $rondaActual AND $concurso['ID_CATEGORIA'] == $categoriaActual) {
			usleep(300000);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
			if($log->rondasTerminadas($concurso['ID_CONCURSO'])){
				$termino = 1;
				break;
			}
		}
		// do empate
		$empate = 0;
		$info_empate = null;
		if($termino == 1){
			$tablero = new TableroPuntaje();
			$info_empate = $tablero->esEmpate($sesion->getOne(SessionKey::ID_CONCURSO));
			if($info_empate['estado'] == 1){
				$empate = 1;
			}
		}
		$cambio = ['estado'=>1,
					'yo_concursante' => $sesion->getOne(SessionKey::ID_CONCURSANTE),
					'mensaje'=>'Cambio de ronda',
					'ronda'=>$concurso['ID_RONDA'],
					'etapa'=>$concurso['ID_ETAPA'],
					'termino'=>$termino,
					'empate'=>$empate,
					'info_empate'=>$info_empate,
					'categoria'=>$concurso['ID_CATEGORIA']];
					
		echo json_encode($cambio);
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}
?>