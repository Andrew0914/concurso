<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Concursante.php';

	class TableroPaso extends BaseTable{

		protected $table = 'tablero_pasos';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Notifica si ya ha sido contestada la pregunta de paso y genera la contabilidad de tiempo trasncurrido
		 * @param integer $concurso
		 * @param integer $ronda
		 * @param integer $pregunta
		 * @param integer $concursante
		 */
		public function pasoContestado($concurso,$ronda,$pregunta,$concursante){
			$objConcursante = new Concursante();	
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante];

			$rs = $this->get($where,$whereValues);
			// contabilizamos los segundos
			unset($whereValues['ID_CONCURSANTE']);
			$queryTiempo = "UPDATE preguntas_generadas SET TIEMPO_TRANSCURRIDO_PASO = TIEMPO_TRANSCURRIDO_PASO + 1 
			WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND ID_PREGUNTA = ? ";
			$this->query($queryTiempo,$whereValues,false);

			if(count($rs) > 0){
				return ['estado' => 1 , 'mensaje'=>'El concursante ha contestado, oprime siguiente para elegir otra pregunta'];
			}
			return ['estado'=> 0 , 'mensaje'=>'Aun no realiza accion el concursante'];
		}

		public function cmp($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
		}

		/**
		 * Obtiene las mejores puntuaiciones y acumulados de las preguntas de paso
		 * @param integer $concurso
		 */
		public function getMejores($concurso){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$sentencia = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,sum(t.PUNTAJE) as totalPuntos 
						FROM tablero_pasos as t INNER JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE 
						WHERE t.ID_CONCURSO = ? ";
				$values = ['ID_CONCURSO'=>$concurso];
				$sentencia .= " GROUP BY c.ID_CONCURSANTE,c.CONCURSANTE ORDER BY totalPuntos DESC ";
				$mejores = $this->query($sentencia,$values,true);
				usort($mejores,array($this,"cmp"));
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
				$response['mejores'] = $mejores;
				$response['estado'] = 1;
				$response['mensaje'] = "Se obtuvo el puntaje total";
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = "No se obtuvo el puntaje total:" . $ex->getMessage();
			}
			return $response;
		}

		/**
		 * Obtiene el tablero de las preguntas de paso
		 * @param integer $concurso
		 */
		public function getResultados($concurso){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,t.PREGUNTA_POSICION,p.PREGUNTA,r.INCISO , r.RESPUESTA,t.PUNTAJE,r.ES_IMAGEN,ro.RONDA,ca.CATEGORIA FROM tablero_pasos as t LEFT JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE LEFT JOIN preguntas as p ON t.PREGUNTA = p.ID_PREGUNTA LEFT JOIN respuestas as r ON t.RESPUESTA = r.ID_RESPUESTA LEFT JOIN rondas ro ON t.ID_RONDA = ro.ID_RONDA INNER JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA WHERE t.ID_CONCURSO = ?";
				$values = [':ID_CONCURSO'=>$concurso];
				$tablero = $this->query($query,$values,true);
				$response['tablero'] = $tablero;
				$response['estado'] = 1;
				$response['mensaje'] = "Se obtuvo el puntaje";
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = "No se obtuvo el puntaje:" . $ex->getMessage();
			}
			return $response;
		}

		/**
		 * Verifica si ya exite en tablero la pregunta de paso
		 * @param array $valores
		 */
		public function existeEnTablero($valores){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ?  AND PREGUNTA = ?";
			return count($this->get($where, $valores)) > 0;
		}

		/**
		 * Almacena la respuesta de la pregunta de paso
		 * @param integer $concursante
		 * @param integer $concurso
		 * @param integer $ronda
		 * @param integer $pregunta
		 * @param integer $respuesta
		 * @param integer $posicion
		 */
		public function guardarRespuestaPaso($concursante,$concurso,$ronda,$pregunta,$respuesta,$posicion){
			//validamos si no respondio
			if($respuesta == ''){
				$respuesta = null;
			}
			try{
				// verificamos que este registrado ya en tablero
				$whereValues = ['ID_CONCURSO'=>$concurso, 'ID_RONDA'=>$ronda ,'ID_CONCURSANTE'=>$concursante
						,'PREGUNTA'=>$pregunta];
				$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ? AND PREGUNTA = ?";
				$registroPaso = $this->get($where , $whereValues);

				if(count($registroPaso) <= 0){
					return ['estado'=> 0 , 'mensaje'=> 'Parece que no se te ha pasado la pregunta correctamente :('];
				}
				$registroPaso = $registroPaso[0];
				$values = ['RESPUESTA' => $respuesta, 'PREGUNTA_POSICION' => $posicion ,'CONTESTADA'=>1]  ;
				// generamos el valor para el campo de respuesta_correcta
				$objRespuesta = new Respuestas();
				if($objRespuesta->esCorrecta($pregunta, $respuesta)){
					$values['RESPUESTA_CORRECTA'] = 1;
				}else{
					$values['RESPUESTA_CORRECTA'] = 0;
				}
				// actualizamos la respuesta
				if($this->update($registroPaso['ID_TABLERO_PASO'],$values)){
					// generamos el puntaje
					if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$values['RESPUESTA_CORRECTA'])){
						return ['estado'=>1, 'mensaje'=>'Respuesta almacenada con exito'];
					}
				}
			}catch(Exception $ex){
				return ['estado'=>0 , 'mensaje'=>'No se almaceno tu respuesta:'.$ex->getMessage()];
			}

			return ['estado'=>0 , 'mensaje'=>'No se almaceno tu respuesta'];
		}

		/**
		 * Genera el puntaje para la pregunta de paso
		 * @param integer $concursante
		 * @param integer $concurso
		 * @param integer $ronda
		 * @param integer $pregunta
		 * @param integer $respuesta
		 * @param integer $correcta
		 * @param integer $paso
		 */
		public function generaPuntaje($concursante,$concurso,$ronda,$pregunta,$respuesta,$correcta,$paso = 0){
			$v_puntaje = ['PUNTAJE'=>0];
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ? ";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'PREGUNTA'=> $pregunta, 'ID_CONCURSANTE' => $concursante];
			$regla = new Reglas();
			$reglas = $regla->getReglasByRonda($ronda);
			$objPregunta = new Preguntas();
			$v_puntaje['PUNTAJE'] = $objPregunta->getPuntajeDificultad($pregunta);
			if($reglas[0]['TIENE_PASO'] == 1 AND $paso == 1 AND $reglas[0]['RESTA_PASO'] ){
				$v_puntaje['PUNTAJE'] *= -1;
			}else if($correcta == 0 AND $reglas[0]['RESTA_ERROR'] == 1){
				$v_puntaje['PUNTAJE'] *= -1;
			}
			
			return $this->update(0, $v_puntaje, $where, $whereValues);
		}

		/**
		 * Obtiene el puntaje de la pregunta para el concursante
		 * @param integer $concurso
		 * @param integer $ronda
		 * @param integer $concursante
		 * @param integer $pregunta
		 */
		public function miPuntajePregunta($concurso,$ronda,$concursante,$pregunta){
			$respone = ['estado'=>0 , 'mensaje'=>'No se pudo obtener el puntaje de tu pregunta'];
			$where = "ID_CONCURSO = ?  AND ID_RONDA= ? AND ID_CONCURSANTE = ? AND PREGUNTA = ?  ";
			$valores = ['ID_CONCURSO' => $concurso  , 
						'ID_RONDA' => $ronda, 
						'ID_CONCURSANTE' => $concursante,
						'PREGUNTA' => $pregunta];
			try{
				$response['puntaje'] = $this->get($where , $valores)[0];
				$response['mensaje']= 'Puntaje obtenido de tu pregunta';
				$response['estado'] = 1;
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'No se obtuvo tu puntaje :'. $ex->getMessage();
			}

			return $response;
		}

		public function eliminar($id=0,$where="",$whereValues = []){
			return $this->delete($id,$where,$whereValues);
		}

		/**
		 * Almacena la respuesta previa de la pregunta tomada por paso
		 * @param array $valores
		 */
		public function preRespuestaPaso($valores){
			$concursante = new Concursante();
			$siguienteConcursante = $concursante->siguiente($valores['ID_CONCURSANTE'],$valores['ID_CONCURSO']);
			$valores['ID_CONCURSANTE'] = $siguienteConcursante['ID_CONCURSANTE'];
			return $this->save($valores);
		}

		public function generaPuntajeTiempoFinalizado($data){
			$concursante = new Concursante();
			$siguienteConcursante = $concursante->siguiente($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO']);
			return $this->generaPuntaje($siguienteConcursante['ID_CONCURSANTE'],$_POST['ID_CONCURSO'] , $_POST['ID_RONDA'],$_POS['ID_PREGUNTA'],'',0,1);
		}
	}

	/**
	 * GET REQUESTS
	 */
	if(isset($_GET['functionTableroPaso'])){
		$function = $_GET['functionTableroPaso'];
		$tablero = new TableroPaso();
		switch ($function) {
			case 'pasoContestado':
				echo json_encode($tablero->pasoContestado($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],
						$_GET['PREGUNTA'],$_GET['ID_CONCURSANTE']));
			break;
			case 'miPuntajePregunta': 
				echo json_encode($tablero->miPuntajePregunta($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],
					$_GET['ID_CONCURSANTE'],$_GET['PREGUNTA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO_PASO:GET']);
		}
	}

	/**
	 * POST REQUEST
	 */
	
	if(isset($_POST['functionTableroPaso'])){
		$function = $_POST['functionTableroPaso'];
		$tablero = new TableroPaso();
		switch ($function) { 
			case 'guardarRespuestaPaso':
 				echo json_encode($tablero->guardarRespuestaPaso($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA'],$_POST['PREGUNTA_POSICION']));
				break;
			case 'generaPuntajeTiempoFinalizado':
				if($tablero->generaPuntajeTiempoFinalizado($_POST)){
					echo json_encode(['estado'=>1,'mensaje'=>'Se genero el puntaje para la pregunta de paso']);
				}else{
					echo json_encode(['estado'=>0,'mensaje'=>'No se genero el puntaje final de paso,vuelve a intentar']);
				}
			break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO_PASO:POST']);
		}
	}
 ?>