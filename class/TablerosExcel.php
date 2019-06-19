<?php 
	require_once dirname(__FILE__) . '/excel/PHPExcel.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';
	require_once dirname(__FILE__) . '/Concursante.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Respuestas.php';

	class TablerosExcel{
		
		public function __construct(){
			PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		}

		private function setEncabezados(&$objSheet , $encabezados){
			$columnas = range('A' , 'Z');
			foreach($encabezados as $columna => $encabezado){
				$objSheet->getCell($columnas[$columna].'1')->setValue($encabezado);
			}
		}

		private function setValues(&$objSheet, $rangoColumnas , $valores, $fila){
			foreach($rangoColumnas as $index => $columna){
				$objSheet->getCell($columna.$fila)->setValue( $valores[$index] );
			}
		}

		/**
		 * Escribe los tableros de puntuaciones generales 
		 */
		private function escribirTablerosGenerales($concurso , &$indexHoja , &$objPHPExcel){
			$objTabMaster = new TableroMaster();
			$tablerosMaster = $objTabMaster->getTablerosMasters($concurso);
			$objTableroPosiciones = new TableroPosiciones();
			$concursante = new Concursante();
			foreach ($tablerosMaster as $tableroMaster) {
				// viene una hoja por defecto se obtiene si queremos mas se debe crear
				if($indexHoja <= 0){
					$objSheet = $objPHPExcel->getSheet($indexHoja);
				}
				else{
					$objSheet = $objPHPExcel->createSheet($indexHoja);
				}
				$objSheet->setTitle('Tablero '.$tableroMaster['ID_TABLERO_MASTER']);
				$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
				$this->setEncabezados($objSheet , ['CONCURSANTE','POSICION','PUNTAJE','EMPATADO']);
				$posiciones = $objTableroPosiciones->obtenerPosicionesTablero($tableroMaster['ID_TABLERO_MASTER']);
				$fila = 2;
				foreach ($posiciones as $posicion) {
					$this->setValues($objSheet , 
									range('A','D') , 
									[$concursante->getConcursante($posicion['ID_CONCURSANTE'])['CONCURSANTE'] ,
										$posicion['POSICION'],
										$posicion['PUNTAJE_TOTAL'],
										$posicion['EMPATADO'] == 1 ? 'SI' : 'NO' ] , $fila);
					$fila++;
				}
				$indexHoja++;
			}

		}
		
		/**
		 * Escribe los tableros de puntaciones a detalle de un concurso
		 */
		private function escribirTablerosPuntuaciones($concurso, &$objPHPExcel, &$indexHoja ){
			$tabPuntaje = new TableroPuntaje();
			$puntajes = $tabPuntaje->getResultados($concurso)['tablero'];
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Puntuaciones Detalle');
			$objSheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12);
			$this->setEncabezados($objSheet , ['RONDA','CONCURSANTE','PREGUNTA','INCISO','RESPUESTA','CATEGORIA','ROBA PUNTOS','PUNTAJE','RONDA EMPATE']);
			$fila = 2;
			foreach ($puntajes as $puntaje) {
				$descripcion_paso = $puntaje['PASO_PREGUNTAS'] =='NO' 
									? $puntaje['PASO_PREGUNTAS'] : 
									$puntaje['PASO_PREGUNTAS'] . ' ' . $puntaje['CONCURSANTE_TOMO'];
				$this->setValues($objSheet,
								range('A' , 'I'),
								[$puntaje['RONDA'],
								$puntaje['CONCURSANTE'],
								$puntaje['PREGUNTA_POSICION'],
								$puntaje['INCISO'],
								$puntaje['RESPUESTA'],
								$puntaje['CATEGORIA'],
								$descripcion_paso,
								$puntaje['PUNTAJE'],
								$puntaje['NIVEL_EMPATE'] == 0 ? 'NO APLICA' :$puntaje['NIVEL_EMPATE'] ],
								$fila);
				$fila++;
			}
		}

		/**
		 * Escribe las preguntas realziadas en el concurso  en una hoja del documento como un glosario
		 */
		private function escribirGlosarioPreguntas($concurso, &$indexHoja, &$objPHPExcel){
			$indexHoja += 1;
			$generadas = new PreguntasGeneradas();
			$glosarios = $generadas->getGlosarioPreguntas($concurso);
			$objSheet = $objPHPExcel->createSheet($indexHoja);
			$objSheet->setTitle('Glosario de preguntas');
			$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
			$this->setEncabezados($objSheet , ['NUMERO','PREGUNTA','RESPUESTA','RONDA','RONDA EMPATE']);
			$fila = 2;
			$objRespuesta = new Respuestas();
			foreach ($glosarios as $glosario) {
				$respuesta = $objRespuesta->verCorrecta($glosario['ID_PREGUNTA']);
				$respuesta = '('. $respuesta['INCISO'].') '.$respuesta['RESPUESTA']; 
				$this->setValues($objSheet , 
								range('A' , 'E'),
								[$glosario['numero'],
								$glosario['pregunta'],
								$respuesta,
								$glosario['ronda'],
								$glosario['empate'] == 0 ? 'NO APLICA' : $glosario['empate']],
								$fila);
				$fila++;
			}
		}

		/**
		 * 	Construye el documento completo del reporte para 1 concurso
		 */
		public function generarExcel($concurso){
			$indexHoja = 0;
			//generamos el objeto de excel
			$objPHPExcel = new PHPExcel;
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$this->escribirTablerosGenerales($concurso ,$indexHoja ,$objPHPExcel);
			$this->escribirTablerosPuntuaciones($concurso,$objPHPExcel,$indexHoja);
			$this->escribirGlosarioPreguntas($concurso,$indexHoja,$objPHPExcel);
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