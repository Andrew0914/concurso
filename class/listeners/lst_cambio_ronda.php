<?php 
	require_once dirname(__FILE__) . '/../util/Sesion.php';
	require_once dirname(__FILE__) . '/../util/SessionKey.php';
	require_once dirname(__FILE__) . '/../Concurso.php';
	require_once dirname(__FILE__) . '/../RondasLog.php';
	require_once dirname(__FILE__) . '/../TableroPuntaje.php';
	require_once dirname(__FILE__) . '/../TableroMaster.php';
	require_once dirname(__FILE__) . '/../TableroPosiciones.php';

	$sesion = new Sesion();
	if($sesion->getOne(SessionKey::ID_CONCURSANTE) > 0 ){
		$rondaActual = $_GET['rondaActual'];
		$categoriaActual = $_GET['categoriaActual'];
		$objConcurso = new Concurso();
		$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
		$log = new RondasLog();
		$termino = 0;
		$tiempo_muerto = 0;
		$bol_tiempo_muerto = 0;
		// Mientras no haya cambiado la ronda
		while($concurso['ID_RONDA'] == $rondaActual AND $concurso['ID_CATEGORIA'] == $categoriaActual) {
			sleep(1);
			if($tiempo_muerto >= 60){
				$bol_tiempo_muerto = 1;
				break;
			}
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
			if($log->rondasTerminadasCategoria($concurso['ID_CONCURSO'],$concurso['ID_CATEGORIA'])){
				$termino = 1;
				break;
			}
			$tiempo_muerto++;
		}
		$cambio = ['estado'=>1,
					'tiempo_muerto'=> $bol_tiempo_muerto,
					'mensaje'=>'Cambio de ronda',
					'ronda'=>$concurso['ID_RONDA'],
					'etapa'=>$concurso['ID_ETAPA'],
					'termino'=>$termino,
					'categoria'=>$concurso['ID_CATEGORIA']];
		echo json_encode($cambio);
		// liberamos memoria
		unset($rondaActual);
		unset($categoriaActual);
		unset($objConcurso);
		unset($concurso);
		unset($log);
		unset($termino);
		unset($tiempo_muerto);
		unset($bol_tiempo_muerto);
		unset($cambio);
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}
	
?>