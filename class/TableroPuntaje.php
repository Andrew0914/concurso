
<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Respuestas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/GradoDificultad.php'; 
	require_once dirname(__FILE__) . '/Concursante.php'; 
	require_once dirname(__FILE__) . '/Reglas.php'; 
	require_once dirname(__FILE__) . '/Preguntas.php'; 
	require_once dirname(__FILE__) . '/TableroPaso.php'; 
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__).'/util/Response.php';

	class TableroPuntaje extends BaseTable{

		protected $table= 'tablero_puntajes';
		private $response;
		public function __construct(){
			parent::__construct();
			$this->response = new Response();
		}

		/**
		 * Metodo que guarda la pre respuesta, los datos antes de confirmar la respuesta
		 * @param  [assoc array] $data 
		 * @return [assoc array]       
		 */
		public function preRespuesta($data){
			$final = $data['final'];
			unset($data['functionTablero']);
			unset($data['final']);
			$data = ['ID_CONCURSO'=>$data['ID_CONCURSO'],
							'ID_RONDA'=>$data['ID_RONDA'],
							'ID_CONCURSANTE'=>$data['ID_CONCURSANTE'],
							'PREGUNTA_POSICION'=>$data['PREGUNTA_POSICION'],
							'PREGUNTA'=>$data['PREGUNTA'],
							'NIVEL_EMPATE'=>$data['NIVEL_EMPATE']];
			if(!$this->existeEnTablero($data)){
				if($final == 1){
					$data['RESPUESTA_CORRECTA'] = 0;
				}
				if($this->save($data)){
					if($final == 1){
						$data['RESPUESTA'] = null;
						$this->generaPuntaje($data['ID_CONCURSANTE'],$data['ID_CONCURSO'],$data['ID_RONDA']
											,$data['PREGUNTA'] , $data['RESPUESTA'],0,0);
					}
					return ['estado' => 1, 'mensaje'=>'Pre respuesta almacenada con exito'];
				}
				return ['estado' => 0, 'mensaje'=>'No se almaceno la pre respuesta'];
			}
			return ['estado' => 2, 'mensaje'=>'Ya existe la pre respuesta almacenada'];	
		}

		/**
		 * Valida si existe en el tablero para no volver a insertarla
		 * @param  [type] $preRespuesta [description]
		 * @return [type]               [description]
		 */
		public function existeEnTablero($preRespuesta){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ? AND PREGUNTA_POSICION = ? AND PREGUNTA = ? AND NIVEL_EMPATE = ?";
			return count($this->get($where, $preRespuesta)) > 0;
		}

		/**
		 * Genera el puntaje por la respuesta y condiciones dada
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
			$puntaje = ['PUNTAJE'=>0];
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ? ";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'PREGUNTA'=> $pregunta, 'ID_CONCURSANTE' => $concursante];
			$regla = new Reglas();
			$reglas = $regla->getReglasByRonda($ronda);
			$objPregunta = new Preguntas();
			$puntaje['PUNTAJE'] = $objPregunta->getPuntajeDificultad($pregunta);
			// regla para paso de preguntas
			if($reglas[0]['TIENE_PASO'] == 1 AND $paso == 1 AND $reglas[0]['RESTA_PASO'] ==1 ){
				$puntaje['PUNTAJE'] *= -1;
				// regla para resta por error
			}else if($correcta == 0 AND $reglas[0]['RESTA_ERROR'] == 1){
				$puntaje['PUNTAJE'] *= -1;
			}else if($correcta == 0 AND $reglas[0]['RESTA_PASO'] ==0 AND $reglas[0]['RESTA_ERROR'] == 0){
				// regla para ronda comun sin negativos
				$puntaje['PUNTAJE'] = 0;
			}
			return $this->update(0, $puntaje, $where, $whereValues);
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
			$total_concursantes = 0;
			$objRonda = new Rondas();
			$objRonda = $objRonda->getRonda($ronda);
			if($objRonda['IS_DESEMPATE'] == 1){
				$master = new TableroMaster();
				$master = $master->getLast($concurso);
				$tabPocisiones = new TableroPosiciones();
				$total_concursantes  = $tabPocisiones->getCountEmpatados($master['ID_TABLERO_MASTER']);
			}else{
				// si no el cumulo son todos los registrados
				$total_concursantes = $concursante->getCountConcursates($concurso)[0]['total'];
			}
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
		public function saveRespuesta($concursante,$concurso,$ronda,$pregunta,$respuesta,$nivel_empate=0,$paso = 0){
			$objRespuesta = new Respuestas();
			$valores = ['RESPUESTA' => $respuesta];
			if($objRespuesta->esCorrecta($pregunta, $respuesta)){
				$valores['RESPUESTA_CORRECTA'] = 1;
			}else{
				$valores['RESPUESTA_CORRECTA'] = 0;
			}
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ? AND NIVEL_EMPATE = ? ";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda 
							, 'PREGUNTA'=> $pregunta, 'ID_CONCURSANTE' => $concursante
							,'NIVEL_EMPATE'=>$nivel_empate];
			if($this->update(0,$valores,$where,$whereValues)){
				if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$valores['RESPUESTA_CORRECTA'],$paso)){
					return $this->response->success([] , 'Respuesta almacenada con exito');
				}
			}
			return $this->response->fail('No se almaceno tu respuesta');
		}

		/**
		 * Devuelve los marcadores por pregunta para el concurso y la ronda
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param  integer $pregunta 
		 * @return array
		 */
		public function getMarcadorPregunta($concurso,$ronda,$pregunta){
			try{
				// obtenenmos lso valores solo en las contestadas
				$sentencia = 'SELECT "totales" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ?
					UNION
					SELECT "correctas" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND RESPUESTA_CORRECTA = 1
					UNION
					SELECT "incorrectas" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND RESPUESTA_CORRECTA = 0';
				$valores =[$concurso,$ronda,$pregunta,$concurso,$ronda,$pregunta,$concurso,$ronda,$pregunta];
				$cantidades = $this->query($sentencia , $valores);

				$totales  = array_filter($cantidades, function ($var){ return ($var['tipo'] == 'totales'); });
				$correctas = array_filter($cantidades, function ($var){ return ($var['tipo'] == 'correctas'); });
				$incorrectas = array_filter($cantidades, function ($var){ return ($var['tipo'] == 'incorrectas'); });

				if($totales <= 0){
					return $this->response->success(['incorrectas' =>0,
													'correctas' =>0,
													'por_incorrectas' =>0,
													'por_correctas' =>0] , 
												'Marcadores obtenidos aun no contesta nadie');
				}
				return $this->response->success(['incorrectas' =>  $incorrectas,
												'correctas' => $correctas,
												'por_incorrectas' => (($incorrectas * 100) / $totales),
												'por_correctas' => (($correctas * 100) / $totales)] , 
											'Marcadores obtenidos');
			}catch(Exception $ex){
				return $this->response->fail('Fallo al obtener marcadores:'.$ex->getMessage());
			}
		}
		
		/**
		 * Genera la informacion para el tablero de resumen general
		 * @param  integer  $concurso   
		 * @param  boolean $es_empate  
		 * @param  boolean $preliminar 
		 * @return array             
		 */
		public function getResultados($concurso,$es_empate = false,$preliminar = false){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT * FROM (SELECT r.ID_RONDA,r.RONDA,c.CONCURSANTE,p.PREGUNTA,w.INCISO,w.RESPUESTA,w.ES_IMAGEN,ca.CATEGORIA,
				(CASE 
					WHEN tp.PASO_PREGUNTA = 0 THEN 'NO'
					WHEN tp.PASO_PREGUNTA = 1 THEN CONCAT('Paso pregunta a ' , (SELECT CONCURSANTE FROM concursantes cp WHERE cp.ID_CONCURSANTE = tp.CONCURSANTE_PASO ))
					WHEN tp.PASO_PREGUNTA = 2 THEN CONCAT('Incorrecta y paso a ' , (SELECT CONCURSANTE FROM concursantes cp WHERE cp.ID_CONCURSANTE = tp.CONCURSANTE_PASO ))
				END )  AS PASO_PREGUNTAS ,
				tp.PUNTAJE,tp.NIVEL_EMPATE,tp.PREGUNTA_POSICION,tp.PASO_PREGUNTA as PASO, if(tp.CONCURSANTE_TOMO = 1,' y SI tomo la preguna','y NO tomo la pregunta') CONCURSANTE_TOMO
				FROM tablero_puntajes tp 
				LEFT JOIN rondas r ON tp.ID_RONDA = r.ID_RONDA
				LEFT JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE
				LEFT JOIN preguntas p ON tp.PREGUNTA = p.ID_PREGUNTA
				LEFT JOIN respuestas w ON tp.RESPUESTA = w.ID_RESPUESTA
				LEFT JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA
				WHERE tp.ID_CONCURSO = ?";
				$values = ['ID_CONCURSO'=>$concurso];
				if($es_empate){
					$query.= " AND tp.ID_RONDA = ? AND tp.NIVEL_EMPATE = ?";
					$values['ID_RONDA'] = $rondas->getRondaDesempate($objConcurso['ID_ETAPA'])['ID_RONDA'];
					$values['NIVEL_EMPATE'] = $objConcurso['NIVEL_EMPATE'];
				}else if($preliminar){
					$query.= " AND tp.ID_RONDA = ? AND tp.NIVEL_EMPATE = ?";
					$values['ID_RONDA'] = $objConcurso['ID_RONDA'];
					$values['NIVEL_EMPATE'] = $objConcurso['NIVEL_EMPATE'];
				}
				$values[':ID_CONCURSO'] = $concurso;
				$values[':ID_RONDA'] = $objConcurso['ID_RONDA'];
				$query.= " UNION ALL ";
				$query.= " SELECT r.ID_RONDA,r.RONDA,c.CONCURSANTE,p.PREGUNTA,w.INCISO,w.RESPUESTA,w.ES_IMAGEN,ca.CATEGORIA
								,'ROBA PUNTOS' AS PASO_PREGUNTAS,tps.PUNTAJE,'0' as NIVEL_EMPATE,tps.PREGUNTA_POSICION,'0' PASO,'' CONCURSANTE_TOMO
					FROM tablero_pasos tps
					LEFT JOIN rondas r ON tps.ID_RONDA = r.ID_RONDA
					LEFT JOIN concursantes c ON tps.ID_CONCURSANTE = c.ID_CONCURSANTE
					LEFT JOIN preguntas p ON tps.PREGUNTA = p.ID_PREGUNTA
					LEFT JOIN respuestas w ON tps.RESPUESTA = w.ID_RESPUESTA
					LEFT JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA
					WHERE tps.ID_CONCURSO = ? AND tps.ID_RONDA = ?) resultados ORDER BY resultados.ID_RONDA,resultados.PREGUNTA_POSICION";
				//echo json_encode($values);
				$tablero = $this->query($query,$values,true);
				return $this->response(['tablero' => $tablero], "Se obtuvo el puntaje");
			}catch(Exception $ex){
				return $this->response->fail("No se obtuvo el puntaje:" . $ex->getMessage());
			}
		}

		/**
		 * Ordena los lugares por total de puntajes
		 * @param  object $a 
		 * @param  object $b 
		 * @return boolean    
		 */
		public function cmp($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
		}

		/**
		 * Genera la informacion para el tablero de marcadores generales
		 * @param  integer $concurso 
		 * @param  boolean $es_empate    
		 * @return array           
		 */
		public function getMejoresPuntajes($concurso, $es_empate = false){
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,sum(t.PUNTAJE) as totalPuntos 
						FROM tablero_puntajes as t INNER JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE 
						WHERE t.ID_CONCURSO = ? ";
				$values = [':ID_CONCURSO'=>$concurso];

				if($es_empate){
					$query.= " AND t.ID_RONDA = ? AND NIVEL_EMPATE = ? ";
					$values['ID_RONDA'] = $rondas->getRondaDesempate($objConcurso['ID_ETAPA'])['ID_RONDA'];
					$values['NIVEL_EMPATE'] = $objConcurso['NIVEL_EMPATE'];
				}

				$query .= " GROUP BY c.ID_CONCURSANTE,c.CONCURSANTE ORDER BY totalPuntos DESC ";
				$mejores = $this->query($query,$values,true);
				usort($mejores,array($this,"cmp"));
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
				return $this->response->success(['mejores' => $mejores],"Se obtuvo el puntaje total");
			}catch(Exception $ex){
				return $this->response->fail( "No se obtuvo el puntaje total:" . $ex->getMessage());
			}
		}

		/**
		 * Genera la informacion para el tablero de marcadores generales de uan ronda especifica para mostrar
		 * @param  integer $concurso 
		 * @param  integer $ronda  
		 * @param  integer $nivelEmpate    
		 * @return array           
		 */
		public function getMejoresRonda($concurso, $ronda, $nivelEmpate){
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,sum(t.PUNTAJE) as totalPuntos 
						FROM tablero_puntajes as t INNER JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE 
						WHERE t.ID_CONCURSO = ? AND t.ID_RONDA = ? AND NIVEL_EMPATE = ?
						GROUP BY c.ID_CONCURSANTE,c.CONCURSANTE ORDER BY totalPuntos DESC";
				$values = [':ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'NIVEL_EMPATE'=> $nivelEmpate];
				$mejores = $this->query($query,$values,true);
				usort($mejores,array($this,"cmp"));
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
				return $this->response->success(['mejores'=> $mejores] , "Se obtuvo el puntaje total");
			}catch(Exception $ex){
				return $this->response->fail("No se obtuvo el puntaje total:" . $ex->getMessage());
			}
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
				$sentencia = 'SELECT COUNT(*) total FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ?';
				$valores =['ID_CONCURSO'=>$concurso,'ID_RONDA'=>$ronda, 'PREGUNTA'=>$pregunta];
				// preguntamos si la ronda es empate para solo tomar encuenta a los ultimos concursantes como participantes activos
				$objRonda = new Rondas();
				$objRonda = $objRonda->getRonda($ronda);
				$countConcursantes = 0;
				// si es una ronda de empate el cumulo total de concursantes son solo los empatados
				if($objRonda['IS_DESEMPATE'] == 1){
					$master = new TableroMaster();
					$master = $master->getLast($concurso);
					$tabPocisiones = new TableroPosiciones();
					$countConcursantes  = $tabPocisiones->getCountEmpatados($master['ID_TABLERO_MASTER']);
				}else{
					// si no el cumulo son todos los registrados
					$countConcursantes  = $concursante->getCountConcursates($concurso)[0]['total'];
				}
				$prTablero = $this->query($sentencia , $valores)[0]['total'];
				// CONTESTADAS
				$response['contestadas']= $prTablero;
				if($prTablero <= 0){
					$response['porcentaje_contestadas'] = 0;
				}else{
					$response['porcentaje_contestadas'] = ($prTablero * 100) / $countConcursantes;
				}
				// no contestadas
				if($countConcursantes > $prTablero){
					$diferencia = ($countConcursantes - $prTablero);
					$response['no_contestadas'] = $diferencia;
					$response['por_no_contestadas'] = (($diferencia * 100) / $countConcursantes);
				}else{
					$response['no_contestadas'] = 0;
					$response['por_no_contestadas'] = 0;
				}
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

		/**
		 * Obtiene el puntaje de la ultima pregunta contestada para el concursante
		 * @param  integer $concurso     
		 * @param  integer $ronda        
		 * @param  integer $concursante  
		 * @param  integer $pregunta     
		 * @param  integer $nivel_empate 
		 * @return array               
		 */
		public function miPuntajePregunta($concurso,$ronda,$concursante,$pregunta,$nivel_empate){
			$respone = ['estado'=>0 , 'mensaje'=>'No se pudo obtener el puntaje de tu pregunta'];
			$where = "ID_CONCURSO = ?  AND ID_RONDA= ? AND ID_CONCURSANTE = ? AND PREGUNTA = ? AND NIVEL_EMPATE = ? ";
			$valores = ['ID_CONCURSO' => $concurso  , 
						'ID_RONDA' => $ronda, 
						'ID_CONCURSANTE' => $concursante,
						'PREGUNTA' => $pregunta,
						'NIVEL_EMPATE' => $nivel_empate];
			try{
				return $this->response->success(['puntaje' => $this->get($where , $valores)[0]],'Puntaje obtenido de tu pregunta');
			}catch(Exception $ex){
				return $this->response->fail('No se obtuvo tu puntaje :'. $ex->getTraceAsString());
			}
		}

		/**
		 * Almacena la respuesta y genera el puntaje de manera directa sin pre respuesta
		 * @param  integer $concursante 
		 * @param  integer $concurso    
		 * @param  integer $ronda       
		 * @param  integer $pregunta    
		 * @param  integer $respuesta   
		 * @param  integer $paso        
		 * @return array              
		 */
		public function guardaRespuestaAsignada($concursante,$concurso,$ronda,$pregunta,$respuesta,$posicion,$paso=0,$nivel_empate){
			//Validamos la variable de la respuesta
			if($respuesta==''){
				$respuesta = null;
			}
			try{
				// buscamos la pregunta preguardada
				$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ? AND PREGUNTA_POSICION = ? AND PREGUNTA= ? AND NIVEL_EMPATE = ?";
				$whereValues = ['ID_CONCURSO'=>$concurso
						,'ID_RONDA'=>$ronda 
						,'ID_CONCURSANTE'=>$concursante
						,'PREGUNTA_POSICION'=>$posicion
						,'PREGUNTA'=>$pregunta
						,'NIVEL_EMPATE'=>$nivel_empate];
				$registroTablero = $this->get($where,$whereValues);
				if(count($registroTablero) <= 0){
					return $this->response->fail('Vaya parece que no se gere tu asignacion de pregunta correctamente');
				}				
				$registroTablero = $registroTablero[0];

				// Verificamos si la respuesta indicada es la correcta
				$objRespuesta = new Respuestas();
				$values = ["RESPUESTA"=>$respuesta , "CONTESTADA" => 1];
				if($objRespuesta->esCorrecta($pregunta, $respuesta)){
					$values['RESPUESTA_CORRECTA'] = 1;
				}else{
					$values['RESPUESTA_CORRECTA'] = 0;
				}
				if($this->update($registroTablero['ID_TABLERO_PUNTAJE'] , $values )){
					if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$values['RESPUESTA_CORRECTA'],$paso)){
						return $this->response->success([],'Respuesta almacenada con exito');
					}
					return $this->response->fail('No se genero el puntaje para la respuesta correctamente');
				}
				return $this->response->fail('No se genero la respuesta');
			}catch(Exception $ex){
				return $this->response->fail('No se almaceno tu respuesta:'.$ex->getMessage());
			}
		}

		/**
		 * Efectua el calculo de la informacion necesaria paea el paso de pregunta
		 * @param   $concursante
		 * @param   $concurso
		 * @param   $ronda
		 * @param   $pregunta
		 * @param   $paso 
		 * @return  array 
		 */
		public function paso($concursante,$concurso,$ronda,$pregunta,$posicion,$paso =1,$nivel_empate){
			$objConcursante = new Concursante();
			try{
				$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_CONCURSANTE'=>$concursante 
										, 'ID_RONDA'=>$ronda , 'PREGUNTA'=>$pregunta];
				$where = "ID_CONCURSO = ? AND ID_CONCURSANTE = ? AND ID_RONDA = ? AND PREGUNTA = ?";
				$valoresPaso = ['PASO_PREGUNTA'=>$paso 
						, 'CONCURSANTE_PASO'=> $objConcursante->siguiente($concursante,$concurso)['ID_CONCURSANTE']];
				// paso directo si almacena pregunta primero
				if($paso == 1){
					if($this->guardaRespuestaAsignada($concursante, $concurso, $ronda, $pregunta, '', $posicion,$paso,$nivel_empate)['estado'] == 1){
						if($this->update(0,$valoresPaso ,$where , $whereValues)){
							return $this->response->success([] , 'Pregunta pasada al siguiente concursante');
						}
					}else{
						return $this->response->fail('No se pudo pasar la pregunta');
					}
				}else if($paso == 2){
					// ya almaceno cuando contesto incorrecto solo se actualiza el paso
					if($this->update(0,$valoresPaso ,$where , $whereValues)){
						return $this->response->success([] , 'Pregunta pasada al siguiente concursante');
					}
				}
				
			}catch(Exception $ex){
				return $this->response->fail('Ocurrio un error al pasar:'.$ex->getMessage());
			}
			return $this->response->fail('No se pudo pasar la pregunta');
		}

		/**
		 * Obtiene la pregunta de que le pasaron al concursane en cuestion
		 * @param  integer $concurso    
		 * @param  integer $concursante 
		 * @param  integer $ronda       
		 * @return array              
		 */
		public function obtenerPreguntaPaso($concurso,$concursante,$ronda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND CONCURSANTE_PASO = ?  ORDER BY ID_TABLERO_PUNTAJE DESC LIMIT 1";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 'CONCURSANTE_PASO'=> $concursante];
			$result = $this->get($where,$whereValues);

			if(count($result) <= 0) return $this->response->fail('No te han pasado ninguna pregunta');
			
			$ultimaPreguntaPaso = $result[0];
	
			if($ultimaPreguntaPaso['CONCURSANTE_TOMO'] != 1) return $this->response->fail('No has tomado ninguna pregunta');
			
			// verificamos si su ultima ya la tiene contestada
			$tabPaso= new TableroPaso();
			$valoresContestada = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 'ID_CONCURSANTE'=>$concursante
								,'PREGUNTA'=>$ultimaPreguntaPaso['PREGUNTA']];
			$wheresContestada = "ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CONCURSANTE = ? AND PREGUNTA = ?";
			$registroPaso = $tabPaso->get($wheresContestada , $valoresContestada);

			if(count($registroPaso) <= 0) return $this->response->fail('No se genero el registro de tu pregunta de roba puntos :(');
			
			if($registroPaso[0]['CONTESTADA'] == 1) return $this->response->fail('Tu ultima pregunta de roba puntos ya la contestaste');
			
			$sentencia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA,pg.TIEMPO_TRANSCURRIDO_PASO
			 FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ?  AND ID_CONCURSANTE = ? AND p.ID_PREGUNTA = ?";
			$valores = ['ID_CONCURSO'=>$concurso 
						, 'ID_RONDA'=>$ronda
						, 'ID_CONCURSANTE'=>$ultimaPreguntaPaso['ID_CONCURSANTE']
						,'ID_PREGUNTA'=>$ultimaPreguntaPaso['PREGUNTA']];
			$pregunta = $this->query($sentencia,$valores,true);
			$objRespuesta = new Respuestas();
			$respuestas = $objRespuesta->getRespuestasByPregunta($pregunta[0]['ID_PREGUNTA']);
			$pregunta['respuestas'] = $respuestas;

			return $this->response->success(['pregunta' => $pregunta], 'Pregunta de paso obtenida');
		}

		/**
		 * Informa si el concursante paso o contesto la pregunta
		 * @param  integer $concurso    
		 * @param  integer $ronda       
		 * @param  integer $pregunta    
		 * @param  integer $concursante 
		 * @return array              
		 */
		public function contestoOpaso($concurso,$ronda,$pregunta,$concursante){
			// obtenemos la inforamcion del tablero de pubtajes
			$objConcursante = new Concursante();	
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante];
			$puntajes = $this->get($where,$whereValues);
			//agregamos un segundo pasado a la pregunta para que el concursante solo tengalos segundos resantes
			$queryTiempo = "UPDATE preguntas_generadas SET TIEMPO_TRANSCURRIDO = TIEMPO_TRANSCURRIDO + 1 
							WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND ID_PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$this->query($queryTiempo,$whereValues,false);
			// verificamos si ya esta insertada la accion de contestado o paso
			if(count($puntajes) > 0){
				if($puntajes[0]['RESPUESTA'] != null AND $puntajes[0]['RESPUESTA'] != '' AND $puntajes[0]['PASO_PREGUNTA'] != 1){
					if($puntajes[0]['RESPUESTA_CORRECTA'] == 1){
						return ['estado' => 1 , 'mensaje'=>'El concursante ha contestado, oprime siguiente para elegir otra pregunta'];
					}else{
						return ['estado'=>2 
						, 'mensaje'=>'El equipo actual ha contestado mal, quiere robar el siguiente equipo: '
						, 'concursante'=> $objConcursante->siguiente($concursante,$concurso) ];
					}
				}
				// SE GENERA UN PASO DE PREGUNTAS POR PASO DIRECTO = 1 Y POR ERROR = 2
				if($puntajes[0]['PASO_PREGUNTA'] == 1 || $puntajes[0]['PASO_PREGUNTA'] == 2){
					return ['estado'=>2 
						, 'mensaje'=>'El equipo actual ha pasado la pregunta, la quiere tomar el equipo: '
						, 'concursante'=> $objConcursante->siguiente($concursante,$concurso) ];
				}
			}

			return ['estado'=> 0 , 'mensaje'=>'Aun no realiza accion el concursante'];
		}

		/**
		 * Actualiza el registro para indicar que el concursante tomo el paso
		 * @param  integer $concurso    
		 * @param  integer $ronda       
		 * @param  integer $pregunta    
		 * @param  integer $concursante 
		 * @return array              
		 */
		public function tomoPaso($concurso,$ronda,$pregunta,$concursante,$posicion){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante,'PREGUNTA_POSICION'=>$posicion];
			$tabPaso = new TableroPaso();
			// primero generamos la pre respuesta para que este contabilizada
			if(!$tabPaso->preRespuestaPaso($whereValues)){
				return $this->response->fail('No pudo ser establecida la pregunta de paso :(');
			}
			unset($whereValues['PREGUNTA_POSICION']);
			if($this->update(0,['CONCURSANTE_TOMO'=>1] , $where , $whereValues)){
				return $this->response->success([] , 'Pregunta tomada para el siguiente concursante');
			}
			return $this->response->fail('No se pudo dar por tomada la pregunta');
		}

		/**
		 * Elimina los puntajes
		 */
		public function eliminar($id,$where,$whereValues){
			return $this->delete($id,$where,$whereValues);
		}

		/**
		 * Genera el registro previo en el tablero de la pregunta asignada para el concursante en la 2da ronda grupal
		 * @param integer $concurso
		 * @param integer $ronda
		 * @param integer $concursante
		 * @param integer $pregunta
		 * @param integer $posicion
		 * @param integer $nivel_empate
		 */
		public function preRespuestaPorAsignacion($concurso,$ronda,$concursante,$pregunta,$posicion,$nivel_empate){
			try{
				// Valores de la pre respuesta del concursante
				$values = [	'ID_CONCURSO'=>$concurso,
							'ID_RONDA'=>$ronda,
							'ID_CONCURSANTE'=>$concursante,
							'PREGUNTA_POSICION'=>$posicion,
							'PREGUNTA'=>$pregunta,
							'NIVEL_EMPATE'=>$nivel_empate ];
				// verificamos si no existe en tablero ya para evitar duplicados
				if($this->existeEnTablero($values)){
					return ['estado'=>2 , 'mensaje'=> "La preguna ya existe en el tablero"];
				}
				// almacenamos la respuesta previa
				if($this->save($values)){
					return $this->response->success([],"Se almaceno con exito el tablero de la pregunta asignada");
				}
				return $this->response->fail('No se pudo establercer el tablero asignado');
			}catch(Exception $ex){
				return $this->response->fail('No se almaceno tu respuesta:'.$ex->getMessage());
			}
		}

		/**
		 * Genera el puntaje para la pregunta cuando el tiempo se termino
		 * @param array $data
		 */
		public function generaPuntajeTiempoFinalizado($data){
			if($this->generaPuntaje($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'] , $_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],'',0,0)){
				// una vez generado el puntaje por fin de tiempo, generamos la informacion de paso por error
				$concursante = new Concursante();
				$siguienteConcursante = $concursante->siguiente($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO']);
				$where = "ID_CONCURSO = ? AND ID_CONCURSANTE = ? AND ID_RONDA = ? AND PREGUNTA = ?";
				$whereValues = ['ID_CONCURSO' => $_POST['ID_CONCURSO'] , 'ID_CONCURSANTE'=>$_POST['ID_CONCURSANTE'] 
								, 'ID_RONDA'=>$_POST['ID_RONDA'] ,'PREGUNTA'=>$_POST['ID_PREGUNTA'] ];
				$updateValues = ['PASO_PREGUNTA' => 2, 'CONCURSANTE_PASO' => $siguienteConcursante['ID_CONCURSANTE']];
				if($this->update(0, $updateValues , $where, $whereValues)){
					return $this->response->success(['ID_CONCURSANTE' => $siguienteConcursante['ID_CONCURSANTE']] , '
													Se termino el tiempo, quiere robar la pregunta el concursante:'. $siguienteConcursante['CONCURSANTE']);
				}
				return $this->response->fail('No se pudo generar el paso por tiempo terminado');
			}
			return $this->response->fail('No se pudo generar el puntaje por tiempo terminado');
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
				echo json_encode($tablero->saveRespuesta($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA'],$_POST['NIVEL_EMPATE']));
				break;
			case 'tomoPaso':
					echo json_encode($tablero->tomoPaso($_POST['ID_CONCURSO'],$_POST['ID_RONDA'],
						$_POST['PREGUNTA'],$_POST['ID_CONCURSANTE'],$_POST['PREGUNTA_POSICION']));
				break;
			case 'guardaRespuestaAsignada':
				echo json_encode($tablero->guardaRespuestaAsignada($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA']
				,$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA'],$_POST['PREGUNTA_POSICION'],$_POST['PASO'],$_POST['NIVEL_EMPATE']));
				break;
			case 'paso':
				echo json_encode($tablero->paso($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA']
				,$_POST['ID_PREGUNTA'],$_POST['PREGUNTA_POSICION'],$_POST['PASO'],$_POST['NIVEL_EMPATE']));
				break;
			case 'generaPuntajeTiempoFinalizado':
					echo json_encode($tablero->generaPuntajeTiempoFinalizado($_POST));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:POST']);
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
				if($tablero->todosContestaron($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['ID_PREGUNTA'],$_GET['NIVEL_EMPATE'])){
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
			case 'miPuntajePregunta': 
				echo json_encode($tablero->miPuntajePregunta($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],
					$_GET['ID_CONCURSANTE'],$_GET['PREGUNTA'],$_GET['NIVEL_EMPATE']));
				break;
			case 'contestoOpaso':
					echo json_encode($tablero->contestoOpaso($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],
						$_GET['PREGUNTA'],$_GET['ID_CONCURSANTE']));
				break;
			case 'obtenerPreguntaPaso':
				echo json_encode($tablero->obtenerPreguntaPaso($_GET['ID_CONCURSO'],$_GET['ID_CONCURSANTE'],$_GET['ID_RONDA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:GET']);
			break;
		}
	}
 ?>
