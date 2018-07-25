<?php 
	require_once dirname(__FILE__) . '/excel/PHPExcel.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/Concursante.php';
 	error_reporting(E_ALL);
	class TablerosExcel{
		
		public function __construct(){}

		public function generarExcel($concurso){
			$indexHoja = 0;
			//generamos el objeto de excel
			$objPHPExcel = new PHPExcel;
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			// especificamos la primer hoja para iniciar
			$tabMaster = new TableroMaster();
			$masters = $tabMaster->getTablerosMasters($concurso);
			$tabPos = new TableroPosiciones();
			$concursante = new Concursante();
			// obtenemos los tableros generales
			foreach ($masters as $m) {
				if($indexHoja <= 0){
					$objSheet = $objPHPExcel->getSheet($indexHoja);
				}
				else{
					$objSheet = $objPHPExcel->createSheet($indexHoja);
				}
				$objSheet->setTitle('Tablero '.$m['ID_TABLERO_MASTER']);
				$objSheet->getCell('A1')->setValue('CONCURSANTE');
				$objSheet->getCell('B1')->setValue('POSICION');
				$objSheet->getCell('C1')->setValue('PUNTAJE TOTAL');
				$objSheet->getCell('D1')->setValue('EMPATADO');
				$posiciones = $tabPos->obtenerPosicionesActuales($m['ID_TABLERO_MASTER']);
				$indexCelda = 2;
				foreach ($posiciones as $p) {
					$objSheet->getCell('A'.$indexCelda)->setValue( $concursante->getConcursante($p['ID_CONCURSANTE'])['CONCURSANTE'] );
					$objSheet->getCell('B'.$indexCelda)->setValue($p['POSICION']);
					$objSheet->getCell('C'.$indexCelda)->setValue($p['PUNTAJE_TOTAL']);
					if($p['EMPATADO'] == 1){
						$objSheet->getCell('D'.$indexCelda)->setValue('SI');
					}else{
						$objSheet->getCell('D'.$indexCelda)->setValue('NO');
					}
					
					$indexCelda++;
				}
				$indexHoja++;
			}
			//obtenemos lso tableros de puntaciones a dealle
			$tabPuntaje = new TableroPuntaje();
			$tabPaso = new TableroPaso();
			$puntajes = $tabPuntaje->getResultados($concurso)['tablero'];
			$pasos = $tabPaso->getResultados($concurso)['tablero'];
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Puntuaciones generales');
			$objSheet->getCell('A1')->setValue('CONCURSANTE');
			$objSheet->getCell('B1')->setValue('RONDA');
			$objSheet->getCell('C1')->setValue('CATEGORIA');
			$objSheet->getCell('D1')->setValue('PREGUNTA');
			$objSheet->getCell('E1')->setValue('RESPUESTA');
			$objSheet->getCell('F1')->setValue('PUNTAJE');
			$indexCelda = 2;
			foreach ($puntajes as $pu) {
				$objSheet->getCell('A'.$indexCelda)->setValue( $concursante->getConcursante($pu['ID_CONCURSANTE'])['CONCURSANTE'] );
				$objSheet->getCell('B'.$indexCelda)->setValue($pu['RONDA']);
				$objSheet->getCell('C'.$indexCelda)->setValue($pu['CATEGORIA']);
				$objSheet->getCell('D'.$indexCelda)->setValue($pu['PREGUNTA']);
				$objSheet->getCell('E'.$indexCelda)->setValue($pu['RESPUESTA']);
				$objSheet->getCell('F'.$indexCelda)->setValue($pu['PUNTAJE']);
				$indexCelda++;
			}

			$indexHoja += 1;
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Puntuaciones PASO');
			$objSheet->getCell('A1')->setValue('CONCURSANTE');
			$objSheet->getCell('B1')->setValue('RONDA');
			$objSheet->getCell('C1')->setValue('CATEGORIA');
			$objSheet->getCell('D1')->setValue('PREGUNTA');
			$objSheet->getCell('E1')->setValue('RESPUESTA');
			$objSheet->getCell('F1')->setValue('PUNTAJE');
			$indexCelda = 2;
			foreach ($pasos as $pa) {
				$objSheet->getCell('A'.$indexCelda)->setValue( $concursante->getConcursante($pa['ID_CONCURSANTE'])['CONCURSANTE'] );
				$objSheet->getCell('B'.$indexCelda)->setValue($pa['RONDA']);
				$objSheet->getCell('C'.$indexCelda)->setValue($pa['CATEGORIA']);
				$objSheet->getCell('D'.$indexCelda)->setValue($pa['PREGUNTA']);
				$objSheet->getCell('E'.$indexCelda)->setValue($pa['RESPUESTA']);
				$objSheet->getCell('F'.$indexCelda)->setValue($pa['PUNTAJE']);
				$indexCelda++;
			}
	
			$nombre = "tableros_concurso_".$concurso.".xlsx";
			$ruta = "../gen_excel/".$nombre;
			$objWriter->save($ruta);
			return $nombre;
		}
	}

	/**
	 * GET RESQUESTS
	 */
	if(isset($_GET['functionExcel'])){
		$function = $_GET['functionExcel'];
		$excel = new TablerosExcel();
		switch ($function) {
			case 'generarExcel':
				echo $excel->generarExcel($_GET['ID_CONCURSO']);
				break;
			default:
				echo json_decode(['estado'=>0, 'mensaje' => 'funcion no valida GET:EXCEL']);
				break;
		}
	}

 ?>