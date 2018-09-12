
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
			// regla para paso de preguntas
			if($reglas[0]['TIENE_PASO'] == 1 AND $paso == 1 AND $reglas[0]['RESTA_PASO'] ==1 ){
				$v_puntaje['PUNTAJE'] *= -1;
				// regla para resta por error
			}else if($correcta == 0 AND $reglas[0]['RESTA_ERROR'] == 1){
				$v_puntaje['PUNTAJE'] *= -1;
			}else if($correcta == 0 AND $reglas[0]['RESTA_PASO'] ==0 AND $reglas[0]['RESTA_ERROR'] == 0){
				// regla para ronda comun sin negativos
				$v_puntaje['PUNTAJE'] = 0;
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
		 * Informa si el concursante paso o contesto la pregunta
		 * @param  integer $concurso    
		 * @param  integer $ronda       
		 * @param  integer $pregunta    
		 * @param  integer $concursante 
		 * @return array              
		 */
		public function contestoOpaso($concurso,$ronda,$pregunta,$concursante){
			$objConcursante = new Concursante();	
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante];
			$rs = $this->get($where,$whereValues);
			if(count($rs) > 0){
				if($rs[0]['RESPUESTA'] != null AND $rs[0]['RESPUESTA'] != '' AND $rs[0]['PASO_PREGUNTA'] != 1){
					if($rs[0]['RESPUESTA_CORRECTA'] == 1){
						return ['estado' => 1 , 'mensaje'=>'El concursante ha contestado, oprime siguiente para elegir otra pregunta'];
					}else{
						return ['estado'=>2 
						, 'mensaje'=>'El equipo actual ha contestado mal, puede robar el siguiente equipo: '
						, 'concursante'=> $objConcursante->siguiente($concursante,$concurso) ];
					}
					
				}
				if($rs[0]['PASO_PREGUNTA'] == 1){
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
		public function tomoPaso($concurso,$ronda,$pregunta,$concursante){
			$response = ['estado'=>0 , 'mensaje'=>'No se pudo dar por tomada la pregunta'];
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante];
			if($this->update(0,['CONCURSANTE_TOMO'=>1] , $where , $whereValues)){
				$response['estado'] = 1;
				$response['mensaje'] = 'Pregunta tomada para el siguiente concursante';
			}

			return $response;
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
				// obtenenmos lso valores solo en las contestadas
				$sentencia = 'SELECT "totales" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ?
					UNION
					SELECT "correctas" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND RESPUESTA_CORRECTA = 1
					UNION
					SELECT "incorrectas" tipo ,count(*) cantidad FROM tablero_puntajes WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND RESPUESTA_CORRECTA = 0';
				$valores =[$concurso,$ronda,$pregunta,$concurso,$ronda,$pregunta,$concurso,$ronda,$pregunta];
				$rs = $this->query($sentencia , $valores);
				$totales = 0;
				$correctas = 0;
				$incorrectas = 0;
				// asignamos para hacer los calculos
				foreach ($rs as $r) {
					if($r['tipo'] =='totales'){
						$totales = $r['cantidad'];
					}else if($r['tipo'] =='correctas'){
						$correctas = $r['cantidad'];
					}else if($r['tipo'] =='incorrectas'){
						$incorrectas = $r['cantidad'];
					}
				}
				if($totales <= 0){
					$response['incorrectas'] = 0;
					$response['correctas'] = 0;
					$response['por_incorrectas'] = 0;
					$response['por_correctas'] = 0;
				}else{
					$response['incorrectas'] = $incorrectas;
					$response['correctas'] = $correctas;
					$response['por_incorrectas'] = ($incorrectas * 100) / $totales;
					$response['por_correctas'] = ($correctas * 100) / $totales;;
				}
				$response['estado']= 1;
				$response['mensaje']='Marcadores obtenidos con exito';

			}catch(Exception $ex){
				$response['mensaje']= 'Fallo al obtener marcadores:'.$ex->getMessage();
			}
			
			return $response;
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
				$query = "SELECT r.RONDA,c.CONCURSANTE,p.PREGUNTA,w.INCISO,w.RESPUESTA,w.ES_IMAGEN,ca.CATEGORIA,
				IF(tp.PASO_PREGUNTA = 1 ,
					CONCAT('Paso pregunta a ' , (SELECT CONCURSANTE FROM concursantes cp WHERE cp.ID_CONCURSANTE = tp.CONCURSANTE_PASO )),
					'NO')  AS PASO_PREGUNTAS ,
				tp.PUNTAJE,tp.NIVEL_EMPATE
				FROM tablero_puntajes tp 
				LEFT JOIN rondas r ON tp.ID_RONDA = r.ID_RONDA
				LEFT JOIN concursantes c ON tp.ID_CONCURSANTE = c.ID_CONCURSANTE
				LEFT JOIN preguntas p ON tp.PREGUNTA = p.ID_PREGUNTA
				LEFT JOIN respuestas w ON tp.RESPUESTA = w.ID_RESPUESTA
				LEFT JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA
				WHERE tp.ID_CONCURSO = ?";
				$values = [':ID_CONCURSO'=>$concurso];
				if($es_empate){
					$query.= " AND tp.ID_RONDA = ? AND tp.NIVEL_EMPATE = ?";
					$values['ID_RONDA'] = $rondas->getRondaDesempate($objConcurso['ID_ETAPA'])['ID_RONDA'];
					$values['NIVEL_EMPATE'] = $objConcurso['NIVEL_EMPATE'];
				}else if($preliminar){
					$query.= " AND tp.ID_RONDA = ?";
					$values['ID_RONDA'] = $objConcurso['ID_RONDA'];
				}
				$values['ID_CONCURSO'] = $concurso;
				$query.= " UNION ALL ";
				$query.= " SELECT r.RONDA,c.CONCURSANTE,p.PREGUNTA,w.INCISO,w.RESPUESTA,w.ES_IMAGEN,ca.CATEGORIA,'ROBA PUNTOS' AS PASO_PREGUNTAS,tps.PUNTAJE,'0' as NIVEL_EMPATE
					FROM tablero_pasos tps
					LEFT JOIN rondas r ON tps.ID_RONDA = r.ID_RONDA
					LEFT JOIN concursantes c ON tps.ID_CONCURSANTE = c.ID_CONCURSANTE
					LEFT JOIN preguntas p ON tps.PREGUNTA = p.ID_PREGUNTA
					LEFT JOIN respuestas w ON tps.RESPUESTA = w.ID_RESPUESTA
					LEFT JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA
					WHERE tps.ID_CONCURSO = ?";
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
		 * @param  integer $ronda    
		 * @return array           
		 */
		public function getMejoresPuntajes($concurso, $es_empate = false){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
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
				$response['puntaje'] = $this->get($where , $valores)[0];
				$response['mensaje']= 'Puntaje obtenido de tu pregunta';
				$response['estado'] = 1;
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'No se obtuvo tu puntaje :'. $ex->getTraceAsString();
			}

			return $response;
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
		public function saveDirect($concursante,$concurso,$ronda,$pregunta,$respuesta,$posicion,$paso){
			//validamos si no respondio
			if($respuesta==''){
				$respuesta = null;
			}
			$values = ['ID_CONCURSO'=>$concurso,'ID_CONCURSANTE'=>$concursante
						, 'ID_RONDA'=>$ronda ,'PREGUNTA'=>$pregunta , 'RESPUESTA'=>$respuesta , 'PREGUNTA_POSICION'=>$posicion];
			try{
				// generamos el valor para el campo de respuesta_correcta
				$objRespuesta = new Respuestas();
				if($objRespuesta->esCorrecta($pregunta, $respuesta)){
					$values['RESPUESTA_CORRECTA'] = 1;
				}else{
					$values['RESPUESTA_CORRECTA'] = 0;
				}
				if($this->save($values)){
					if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$values['RESPUESTA_CORRECTA'],$paso)){
						return ['estado'=>1, 'mensaje'=>'Respuesta almacenada con exito'];
					}
				}
			}catch(Exception $ex){
				return ['estado'=>0 , 'mensaje'=>'No se almaceno tu respuesta:'.$ex->getMessage()];
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
		public function paso($concursante,$concurso,$ronda,$pregunta,$posicion,$paso =1){
			$objConcursante = new Concursante();
			try{
				if($this->saveDirect($concursante, $concurso, $ronda, $pregunta, '', $posicion,$paso)['estado'] == 1){
					$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_CONCURSANTE'=>$concursante 
									, 'ID_RONDA'=>$ronda , 'PREGUNTA'=>$pregunta];
					$where = "ID_CONCURSO = ? AND ID_CONCURSANTE = ? AND ID_RONDA = ? AND PREGUNTA = ?";
					$valoresPaso = ['PASO_PREGUNTA'=>$paso 
					, 'CONCURSANTE_PASO'=> $objConcursante->siguiente($concursante,$concurso)['ID_CONCURSANTE']];
					if($this->update(0,$valoresPaso ,$where , $whereValues)){
						return ['estado'=>1 , 'mensaje' => 'Pregunta pasada al siguiente concursante'];
					} 
				}else{
					return ['estado'=>0, 'mensaje'=>'No se pudo pasar la pregunta x'];
				}
			}catch(Exception $ex){
				return ['estado'=>0 , 'mensaje'=>'Ocurrio un error al pasar:'.$ex->getMessage()];
			}
			return ['estado'=>0, 'mensaje'=>'No se pudo pasar la pregunta y'];
		}

		/**
		 * Obtiene 
		 * @param  [type] $concurso    [description]
		 * @param  [type] $concursante [description]
		 * @param  [type] $ronda       [description]
		 * @return [type]              [description]
		 */
		public function obtenerPreguntaPaso($concurso,$concursante,$ronda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND CONCURSANTE_PASO = ?  ORDER BY ID_TABLERO_PUNTAJE DESC LIMIT 1;";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 'CONCURSANTE_PASO'=> $concursante];
			$result = $this->get($where,$whereValues);
			if(count($result) <= 0){
				return ['estado'=>0 , 'mensaje'=>'No te han pasado ninguna pregunta'];
			}
			$ultimaPreguntaPaso = $result[0];
	
			if($ultimaPreguntaPaso['CONCURSANTE_TOMO'] != 1){
				return ['estado'=>0 , 'mensaje'=>'No has tomado ninguna pregunta'];
			}

			$tabPaso= new TableroPaso();
			if($tabPaso->existeEnTablero(['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 'ID_CONCURSANTE'=>$concursante,'PREGUNTA'=>$ultimaPreguntaPaso['PREGUNTA']])){
				return ['estado'=>0 , 'mensaje'=>'No te han pasado ninguna pregunta'];
			}

			$sentencia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ?  AND ID_CONCURSANTE = ? AND p.ID_PREGUNTA = ?";
			$valores = ['ID_CONCURSO'=>$concurso 
						, 'ID_RONDA'=>$ronda
						, 'ID_CONCURSANTE'=>$ultimaPreguntaPaso['ID_CONCURSANTE']
						,'ID_PREGUNTA'=>$ultimaPreguntaPaso['PREGUNTA']];
			$pregunta = $this->query($sentencia,$valores,true);
			$objRespuesta = new Respuestas();
			$respuestas = $objRespuesta->getRespuestasByPregunta($pregunta[0]['ID_PREGUNTA']);
			$pregunta['respuestas'] = $respuestas;
			return ['estado'=>1 , 'mensaje'=>'Pregunta de paso obtenida', 'pregunta'=>$pregunta];
		}

		/**
		 * Elimina los puntajes
		 */
		public function eliminar($id,$where,$whereValues){
			return $this->delete($id,$where,$whereValues);
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
						$_POST['PREGUNTA'],$_POST['ID_CONCURSANTE']));
				break;
			case 'saveDirect':
				echo json_encode($tablero->saveDirect($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA'],$_POST['PREGUNTA_POSICION'],$_POST['PASO']));
				break;
			case 'paso':
				echo json_encode($tablero->paso($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['PREGUNTA_POSICION'],$_POST['PASO']));
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
