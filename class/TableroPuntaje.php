<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Respuestas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/GradoDificultad.php'; 
	require_once dirname(__FILE__) . '/Concursante.php'; 
	require_once dirname(__FILE__) . '/Reglas.php'; 
	require_once dirname(__FILE__) . '/Preguntas.php'; 

	class TableroPuntaje extends BaseTable{

		protected $table= 'tablero_puntajes';
		
		public function __construct(){
			parent::__construct();
		}

		/**
		 * Metodo que guarda la pre respuesta, los datos antes de confirmar la respuesta
		 * @param  [assoc array] $data 
		 * @return [assoc array]       
		 */
		public function preRespuesta($preRespuesta){
			unset($preRespuesta['functionTablero']);
			if(!$this->existeEnTablero($preRespuesta)){
				if($this->save($preRespuesta)){
					return ['estado' => 1, 'mensaje'=>'Pre respuesta almacenada con exito'];
				}
				return ['estado' => 0, 'mensaje'=>'No se almaceno la pre respuesta'];
			}
			return ['estado' => 2, 'mensaje'=>'Ya existe la pre respuesta almacenada'];	
		}

		public function existeEnTablero($preRespuesta){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ? AND PREGUNTA_POSICION = ? AND PREGUNTA = ?";
			return count($this->get($where, $preRespuesta)) > 0;
		}

		/**
		 * Genera el punta por la respuesta y condiciones dada
		 * @param  integer  $concursante 
		 * @param  integer  $concurso    
		 * @param  integer  $ronda       
		 * @param  integer  $pregunta    
		 * @param  integer  $respuesta   
		 * @param  integer  $correcta    
		 * @param  integer 	$paso        
		 * @return boolean               
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
			}
			if($correcta == 0 AND $reglas[0]['RESTA_ERROR'] == 1){
				$v_puntaje['PUNTAJE'] *= -1;
			}
			
			return $this->update(0, $v_puntaje, $where, $whereValues);
		}

		/**
		 * Verifica si todos los concursantes de concurso han contestado ya
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param  integer $pregunta 
		 * @return boolean
		 */
		public function todosContestaron($concurso,$ronda,$pregunta){
			$query = "SELECT COUNT(ID_CONCURSANTE) as total FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ?";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'PREGUNTA'=> $pregunta];

			$contestaron = $this->query($query,$valores , true)[0]['total'];
			$concursante = new Concursante();
			$total_concursantes = $concursante->getCountConcursates($concurso)[0]['total'];

			return  $total_concursantes == $contestaron;
		}

		/**
		 * Guarda la respuesta una vez que se genero la pre respuestas
		 * @param  integer  $concursante 
		 * @param  integer  $concurso    
		 * @param  integer  $ronda       
		 * @param  integer  $pregunta    
		 * @param  integer  $respuesta   
		 * @param  integer $paso        
		 * @return array               
		 */
		public function saveRespuesta($concursante,$concurso,$ronda,$pregunta,$respuesta,$paso = 0){
			$objRespuesta = new Respuestas();
			$valores = ['RESPUESTA' => $respuesta];
			if($objRespuesta->esCorrecta($pregunta, $respuesta)){
				$valores['RESPUESTA_CORRECTA'] = 1;
			}else{
				$valores['RESPUESTA_CORRECTA'] = 0;
			}
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ? ";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'PREGUNTA'=> $pregunta, 'ID_CONCURSANTE' => $concursante];
			if($this->update(0,$valores,$where,$whereValues)){
				if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$valores['RESPUESTA_CORRECTA'],$paso)){
					return ['estado'=>1, 'mensaje'=>'Respuesta almacenada con exito'];
				}
			}

			return ['estado'=>0, 'mensaje'=>'No se almaceno tu respuesta'];
		}

		/**
		 * Devuelve los marcadores por pregunta para el concurso y la ronda
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param  integer $pregunta 
		 * @return array
		 */
		public function getMarcadorPregunta($concurso,$ronda,$pregunta){
			$response = ['estado'=>0, 'mensaje'=>'No se obtuvieron los marcadores'];
			try{
				$concursante = new Concursante();
				$sentencia = 'SELECT SUM(CASE WHEN t.RESPUESTA_CORRECTA = 1 then 1 else 0 end) correctas ,
						SUM(CASE WHEN t.RESPUESTA_CORRECTA = 0 then 1 else 0 end) incorrectas
						FROM tablero_puntajes t  WHERE t.ID_CONCURSO = ?
						AND t.ID_RONDA = ? AND t.PREGUNTA = ?;';

				$valores =['ID_CONCURSO'=>$concurso,'ID_RONDA'=>$ronda, 'PREGUNTA'=>$pregunta];
				$response['marcadores']= $this->query($sentencia,$valores,true);
				$response['cont_concursantes'] = $concursante->getCountConcursates($concurso);
				$response['estado']= 1;
				$response['mensaje']='Marcadores obtenidos con exito';

			}catch(Exception $ex){
				$response['mensaje']= 'Fallo al obtener marcadores:'.$ex->getMessage();
			}
			
			return $response;
		}

		/**
		 * Genera la informacion para el tablero de resumen general
		 * @param  integer $concurso
		 * @return array          
		 */
		public function getResultados($concurso,$es_empate = false){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,t.PREGUNTA_POSICION,p.PREGUNTA,r.INCISO , r.RESPUESTA,t.PASO_PREGUNTA,t.PUNTAJE,r.ES_IMAGEN,ro.RONDA,ca.CATEGORIA FROM tablero_puntajes as t LEFT JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE LEFT JOIN preguntas as p ON t.PREGUNTA = p.ID_PREGUNTA LEFT JOIN respuestas as r ON t.RESPUESTA = r.ID_RESPUESTA LEFT JOIN rondas ro ON t.ID_RONDA = ro.ID_RONDA INNER JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA WHERE t.ID_CONCURSO = ?";
				$values = [':ID_CONCURSO'=>$concurso];
				if($es_empate){
					$query.= " AND t.ID_RONDA = ? ";
					$values['ID_RONDA'] = $rondas->getRondaDesempate($objConcurso['ID_ETAPA'])['ID_RONDA'];
				}
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
		 * Genera la informacion para el tablero de marcadores generales
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @return array           
		 */
		public function getMejoresPuntajes($concurso, $es_empate = false){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,sum(t.PUNTAJE) as totalPuntos,@rownum:=@rownum+1 lugar
						FROM tablero_puntajes as t LEFT JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE ,(SELECT @rownum:=0) r
						WHERE t.ID_CONCURSO = ? ";
				$values = [':ID_CONCURSO'=>$concurso];
				if($es_empate){
					$query.= " AND t.ID_RONDA = ? ";
					$values['ID_RONDA'] = $rondas->getRondaDesempate($objConcurso['ID_ETAPA'])['ID_RONDA'];
				}
				$query .= " GROUP BY c.ID_CONCURSANTE ORDER BY totalPuntos DESC ";
				
				$mejores = $this->query($query,$values,true);
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
		 * Obtiene la actividad sobre la pregunta si ya fue contestada o no y por cuantos
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param  integer $pregunta 
		 * @return array           
		 */
		public function getActividadPregunta($concurso,$ronda,$pregunta){
			$response = ['estado'=>0, 'mensaje'=>'No se obtuvieron los marcadores'];
			try{
				$concursante = new Concursante();
				$sentencia = 'SELECT SUM(CASE WHEN NOT ISNULL (t.RESPUESTA)  then 1 else 0 end) contestadas ,
					SUM(CASE WHEN ISNULL(t.RESPUESTA)  then 1 else 0 end) nocontestadas
					FROM tablero_puntajes t  WHERE t.ID_CONCURSO = 	?
					AND t.ID_RONDA = ? AND t.PREGUNTA = ?';

				$valores =['ID_CONCURSO'=>$concurso,'ID_RONDA'=>$ronda, 'PREGUNTA'=>$pregunta];
				$response['marcadores']= $this->query($sentencia,$valores,true);
				$response['cont_concursantes'] = $concursante->getCountConcursates($concurso);
				$response['estado']= 1;
				$response['mensaje']='Marcadores obtenidos con exito';

			}catch(Exception $ex){
				$response['mensaje']= 'Fallo al obtener marcadores:'.$ex->getMessage();
			}
			
			return $response;
		}

		/**
		 * Verifica si existe un empate y en cuyo caso arroja la lista de los empatados
		 * @param  integer $concurso 
		 * @return array
		 */
		public function esEmpate($concurso,$es_empate = false){
			$resultados = $this->getMejoresPuntajes($concurso,$es_empate);
			if($resultados['estado'] == 1){
				$tablero = $resultados['mejores'];
				$empatados = array();
				foreach ($tablero as  $tab) {
					foreach ($tablero as $t) {
						if($tab['ID_CONCURSANTE'] != $t['ID_CONCURSANTE'] AND $tab['totalPuntos'] == $t['totalPuntos']){
							$empatados[] = $tab;
						}
					}
				}
				$empatados = array_unique($empatados, SORT_REGULAR);
				if(count($empatados) > 0){
					return ['estado'=>1,'mensaje'=>'Se genero empate', 'empatados'=>$empatados];
				}

				return ['estado'=>2,'mensaje'=>'No existe empate,puedes cerrar el concurso', 'empatados'=>null]; 
			}
			return ['estado'=>0 , 'mensaje'=> 'No se pudieron determinar los resultados para saber si existe desempate'];
		}
	}
	/**
	 * POST REQUESTS
	 */
	
	if(isset($_POST['functionTablero'])){
		$function = $_POST['functionTablero'];
		$tablero = new TableroPuntaje();
		switch ($function) {
			case 'preRespuesta':
				echo json_encode($tablero->preRespuesta($_POST));
				break;
			case 'saveRespuesta':
				echo json_encode($tablero->saveRespuesta($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:POST']);
				break;
		}
	}

	/**
	 * GET REQUESTS
	 */
	if(isset($_GET['functionTablero'])){
		$function = $_GET['functionTablero'];
		$tablero = new TableroPuntaje();
		switch ($function) {
			case 'todosContestaron':
				if($tablero->todosContestaron($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['ID_PREGUNTA'])){
					echo json_encode(['estado'=>1, 'mensaje'=>'Todos contestaron']);
				}else{
					echo json_encode(['estado'=>0, 'mensaje'=>'Aun falta por contestar']);
				}
			break;
			case 'getMarcadorPregunta':
				echo json_encode($tablero->getMarcadorPregunta($_GET['ID_CONCURSO'], $_GET['ID_RONDA'], $_GET['ID_PREGUNTA']));
			break;
			case 'getActividadPregunta':
				echo json_encode($tablero->getActividadPregunta($_GET['ID_CONCURSO'], $_GET['ID_RONDA'], $_GET['ID_PREGUNTA']));
			break;
			case 'getTableroDisplay':
				echo json_encode($tablero->getTableroDisplay($_GET['ID_CONCURSO'] , $_GET['ID_RONDA']));
				break;
			case 'getMejoresPuntajes':
				echo json_encode($tablero->getMejoresPuntajes($_GET['ID_CONCURSO'] , $_GET['ID_RONDA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:GET']);
			break;
		}
	}
 ?>
