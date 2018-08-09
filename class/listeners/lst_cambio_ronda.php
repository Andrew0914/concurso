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
		$cambio = array();
		$cambio['tiempo_muerto'] = 0;
		// Mientras no haya cambiado la ronda
		while($concurso['ID_RONDA'] == $rondaActual AND $concurso['ID_CATEGORIA'] == $categoriaActual) {
			sleep(1);
			// si ha ocurrido un tiempo muerto de 30 o mas seg / 30 iteraciones rompemos le while
			if($tiempo_muerto >= 30){
				$cambio['tiempo_muerto'] = 1;
				break;
			}
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
			if($log->rondasTerminadasCategoria($concurso['ID_CONCURSO'],$concurso['ID_CATEGORIA'])){
				$termino = 1;
				break;
			}
			$tiempo_muerto++;
		}
		$cambio['estado']=1;
		$cambio['mensaje']='Cambio de ronda';
		$cambio['ronda']=$concurso['ID_RONDA'];
		$cambio['etapa']=$concurso['ID_ETAPA'];
		$cambio['termino']=$termino;
		$cambio['categoria']=$concurso['ID_CATEGORIA'];
		echo json_encode($cambio);
	}else{
		echo json_encode(array('estado'=>0,'mensaje'=>'Fallo la sesion'));
	}
?>