<?php 
	require_once dirname(__FILE__) . '/excel/PHPExcel.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/Concursante.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';

	class TablerosExcel{
		
		public function __construct(){
			PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		}

		public function generarExcel($concurso){
			$indexHoja = 0;
			//generamos el objeto de excel
			$objPHPExcel = new PHPExcel;
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			// especificamos la primer hoja para iniciar
			$tabMaster = new TableroMaster();
			$masters = $tabMaster->getTablerosMasters($concurso);
			$tabPos = new TableroPosiciones();
			$concursante = new Concursante();
			// INICIO TABLEROS DE POSICIONES GENERALES
			foreach ($masters as $m) {
				// viene una hoja por defecto se obtiene si queremos mas se debe crear
				if($indexHoja <= 0){
					$objSheet = $objPHPExcel->getSheet($indexHoja);
				}
				else{
					$objSheet = $objPHPExcel->createSheet($indexHoja);
				}
				$objSheet->setTitle('Tablero '.$m['ID_TABLERO_MASTER']);
				$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
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
			// FIN TABLEROS DE POSICIONES GENERALES
			// INICIO DE TABLERO DE PUNTAJES DETALLE
			$tabPuntaje = new TableroPuntaje();
			$puntajes = $tabPuntaje->getResultados($concurso)['tablero'];
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Puntuaciones Detalle');
			$objSheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12);
			$objSheet->getCell('A1')->setValue('RONDA');
			$objSheet->getCell('B1')->setValue('CONCURSANTE');
			$objSheet->getCell('C1')->setValue('PREGUNTA');
			$objSheet->getCell('D1')->setValue('INCISO');
			$objSheet->getCell('E1')->setValue('RESPUESTA');
			$objSheet->getCell('F1')->setValue('CATEGORIA');
			$objSheet->getCell('G1')->setValue('ROBA PUNTOS');
			$objSheet->getCell('H1')->setValue('PUNTAJE');
			$objSheet->getCell('I1')->setValue('RONDA EMPATE');
			$indexCelda = 2;

			foreach ($puntajes as $pu) {
				$objSheet->getCell('A'.$indexCelda)->setValue($pu['RONDA']);
				$objSheet->getCell('B'.$indexCelda)->setValue($pu['CONCURSANTE']);
				$objSheet->getCell('C'.$indexCelda)->setValue($pu['PREGUNTA_POSICION']);
				$objSheet->getCell('D'.$indexCelda)->setValue($pu['INCISO']);
				$objSheet->getCell('E'.$indexCelda)->setValue($pu['RESPUESTA']);
				$objSheet->getCell('F'.$indexCelda)->setValue($pu['CATEGORIA']);
				
				$descripcion_paso = $pu['PASO_PREGUNTAS'] =='NO' 
									? $pu['PASO_PREGUNTAS'] : 
									$pu['PASO_PREGUNTAS'] . ' ' . $pu['CONCURSANTE_TOMO'];

				$objSheet->getCell('G'.$indexCelda)->setValue($descripcion_paso);
				$objSheet->getCell('H'.$indexCelda)->setValue($pu['PUNTAJE']);
				if($pu['NIVEL_EMPATE'] == 0){
					$objSheet->getCell('I'.$indexCelda)->setValue("No aplica");
				}else{
					$objSheet->getCell('I'.$indexCelda)->setValue($pu['NIVEL_EMPATE']);
				}
				$indexCelda++;
			}
			// FIN TABLERO DE PUNTAJES DETALLE
			// INICIO GLOSARIO DE PREGUNTAS
			$indexHoja += 1;
			$generadas = new PreguntasGeneradas();
			$glosario = $generadas->getGlosarioPreguntas($concurso);
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Glosario de preguntas');
			$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
			$objSheet->getCell('A1')->setValue('NUMERO');
			$objSheet->getCell('B1')->setValue('PREGUNTA');
			$objSheet->getCell('C1')->setValue('RONDA');
			$objSheet->getCell('D1')->setValue('RONDA EMPATE');
			$indexCelda = 2;
			foreach ($glosario as $g) {
				$objSheet->getCell('A'.$indexCelda)->setValue($g['numero']);
				$objSheet->getCell('B'.$indexCelda)->setValue($g['pregunta']);
				$objSheet->getCell('C'.$indexCelda)->setValue($g['ronda']);
				if($pu['NIVEL_EMPATE'] == 0){
					$objSheet->getCell('D'.$indexCelda)->setValue("No aplica");
				}else{
					$objSheet->getCell('D'.$indexCelda)->setValue($g['empate']);
				}
				$indexCelda++;
			}
			//FIN GLOSARIO DE PREGUNTAS
			

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