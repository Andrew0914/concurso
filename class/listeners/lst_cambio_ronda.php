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
		// Mientras no haya cambiado la ronda
		while($concurso['ID_RONDA'] == $rondaActual AND $concurso['ID_CATEGORIA'] == $categoriaActual) {
			sleep(1);
			$concurso = $objConcurso->getConcurso($sesion->getOne(SessionKey::ID_CONCURSO));
			if($log->rondasTerminadasCategoria($concurso['ID_CONCURSO'],$concurso['ID_CATEGORIA'])){
				$termino = 1;
				break;
			}
		}
		// determinamos el empate
		$empate = 0;
		$info_empate = null;
		// solo si ya terminaron las rondas normales
		if($termino == 1){
			// Obtenemos la informacion de los tableros
			$tablero_master = new TableroMaster();
			$mMasters = $tablero_master->getTablerosMasters($concurso['ID_CONCURSO']);
			$ultimoYaTienePosiciones = false;
			$ultimoNoCerrado = false;
			$tabPosiciones = new TableroPosiciones();
			$posiciones = null;
			// esperamso hasta que generen->cierren el tablero->calculen las posiciones
			while (count($mMasters) == 0 || $ultimoNoCerrado|| $ultimoYaTienePosiciones) {
				sleep(1);
				if(count($mMasters) > 0){
					$ultimoNoCerrado = $mMasters[count($mMasters) - 1]['CERRADO'] == 0;
					$posiciones = $tabPosiciones->obtenerPosicionesActuales($mMasters[count($mMasters) - 1]['ID_TABLERO_MASTER']);
				}
				// nos aseguramos que ya hayan sido generadas todas las posiciones por cuestion de timing
				if(count($posiciones) > 0){
					if($mMasters[count($mMasters) - 1]['POSICIONES_GENERADAS'] == 1){
						$ultimoYaTienePosiciones = true;
					}
				}
				$mMasters = $tablero_master->getTablerosMasters($concurso['ID_CONCURSO']);
			}
			if(count($mMasters) > 0){
				$info_empate = $tabPosiciones->esEmpate($mMasters[count($mMasters) - 1]['ID_TABLERO_MASTER']);
				$empate = $info_empate['estado'];
				if($info_empate['estado'] == 1){
					// solo habilitamos los emptates para los primeros 3 lugares si es el caso
					for($x = 0 ; $x < count($info_empate['empatados']) ; $x++ ){
						if($info_empate['empatados'][$x]['POSICION'] > 3){
							unset($info_empate['empatados'][$x]);
						}
					}
				}
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