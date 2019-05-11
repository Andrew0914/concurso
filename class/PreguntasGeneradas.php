<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php'; 
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	require_once dirname(__FILE__) . '/Respuestas.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';


	class PreguntasGeneradas extends BaseTable{

		protected $table = 'preguntas_generadas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Genera las preguntas  para la categoria
		 * @param  integer $idConcurso    
		 * @param  integer $idCategoria 
		 * @param integer $etapa
		 * @return array              
		 */
		public function generaPreguntas($idConcurso, $idCategoria,$etapa){
			$rs = ['estado'=> 0, 'mensaje'=>'NO se generaron las preguntas'];
			$mensaje ="";
			$objRonda = new Rondas();
			$rondas = $objRonda->getRondas($etapa)['rondas'];
			$objRegla = new Reglas();
			$valida= 1;
			foreach ($rondas as $ronda) {
				if($ronda['IS_DESEMPATE'] == 0){
					$idRonda = $ronda['ID_RONDA'];
					if($idRonda == 5){
						continue;
					}
					$regla = $objRegla->getReglasByRonda($idRonda)[0];
					// la cantidad de preguntas por categoria debe considir a la cantidad de grados en el campo
					$grados = explode(',',$regla['GRADOS']);

					if($this->cantidadPreguntasCategoria($idConcurso,$idRonda,$idCategoria) 
						>= $ronda['PREGUNTAS_POR_CATEGORIA']){
						$mensaje .= 'Se han generado todas las preguntas para la categoria de la ronda '.$idRonda .' ; ';
						continue;
					}

					if($this->cantidadPreguntasTotal($idConcurso,$idRonda) >= $ronda['CANTIDAD_PREGUNTAS']){
						$mensaje .= 'Se han generado todas las preguntas para  la ronda '.$idRonda .' ; ';
						continue;
					}

					for($cont = 1 ; $cont <= $ronda['PREGUNTAS_POR_CATEGORIA']; $cont++){
						$preguntas = $this->getPreguntasByCatGrado($idCategoria,$grados[$cont - 1]);
						$key = array_rand($preguntas);
						$preguntaAleatoria = $preguntas[$key];
						while ($this->existePreguntaEnConcursoRonda($idConcurso,
							$preguntaAleatoria['ID_PREGUNTA'])) {
							$preguntas = $this->getPreguntasByCatGrado($idCategoria,$grados[$cont - 1]);
							$preguntaAleatoria = array_rand($preguntas);
						}
						if($preguntaAleatoria['ID_PREGUNTA']  == null || $preguntaAleatoria['ID_PREGUNTA']  == ''){
							$cont -=1;
							continue;
						}
						$valoresInsert = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
							, 'ID_CONCURSO' => $idConcurso 
							, 'ID_RONDA' => $idRonda
							, 'PREGUNTA_POSICION' => ($this->cantidadPreguntasTotal($idConcurso,$idRonda) + 1) ];
							if($this->save($valoresInsert) <= 0){
								$valida *= 0;
							}
					}
				}
			}
			$valida2 = 1;
			if($etapa == 2){
				$valida2 = $this->generaPreguntasGRP($idConcurso,$idCategoria)['estado'];
			}

			if($valida && $valida2){
				if($mensaje ==''){
					$mensaje = "GENERACION DE PREGUNTAS EXITOSA !";
				}

				$rs = ['estado'=> 1,
					'mensaje'=>$mensaje,
					'counts'=> $this->getCantidadGeneradas($etapa,$idConcurso)];
			}else{
				// elimino todas si no se generaron correctamente para volver a intentar
				$this->delete(0,"ID_CONCURSO=? AND ID_CATEGORIA = ?" 
					, ['ID_CONCURSO' => $idConcurso , 'ID_CATEGORIA'=>$idCategoria]);
			}

			return $rs;
		}

		/**
		 * Genera las preguntas para la segunda ronda grupal
		 * @param  integer $idConcurso 
		 * @param  integer $idCategoria
		 * @return array            
		 */
		public function generaPreguntasGRP($idConcurso , $idCategoria){
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$objConcursante = new Concursante();
			$concursantes = $objConcursante->getConcursantes($idConcurso)['concursantes'];
			$objRegla = new Reglas();
			$regla = $objRegla->getReglasByRonda(5)[0];
			$grados = explode(',',$regla['GRADOS']);
			$ronda = new Rondas();
			$ronda = $ronda->getRonda(5);
			$valida= 1;
			foreach ($concursantes as $cnc) {
				for($cont = 1 ; $cont <= $ronda['TURNOS_PREGUNTA_CONCURSANTE']; $cont++){
						$preguntas = $this->getPreguntasByCatGrado($idCategoria,$grados[$cont - 1]);
						$key = array_rand($preguntas);
						$preguntaAleatoria = $preguntas[$key];
						while ($this->existePreguntaEnConcursoRonda($idConcurso,
							$preguntaAleatoria['ID_PREGUNTA'])) {
							$preguntas = $this->getPreguntasByCatGrado($idCategoria,$grados[$cont - 1]);
							$preguntaAleatoria = array_rand($preguntas);
						}
						if($preguntaAleatoria['ID_PREGUNTA']  == null || $preguntaAleatoria['ID_PREGUNTA']  == ''){
							$cont -=1;
							continue;
						}
						$valoresInsert = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
							, 'ID_CONCURSO' => $idConcurso 
							, 'ID_RONDA' => 5
							, 'ID_CONCURSANTE'=>$cnc['ID_CONCURSANTE']
							, 'OLEADA'=>$cont 
							, 'PREGUNTA_POSICION' => ($this->cantidadPreguntasTotal($idConcurso,5) + 1) ];
							if($this->save($valoresInsert) <= 0){
								$valida *= 0;
							}
					}
			}
			if($valida){
				$rs = ['estado'=> 1,
					'mensaje'=>"GENERACION DE PREGUNTAS EXITOSA !"];
			}else{
				// elimino todas si no se generaron correctamente para volver a intentar
				$this->delete(0,"ID_CONCURSO=? AND ID_CATEGORIA = ? AND ID_RONDA = ?" 
					, ['ID_CONCURSO' => $idConcurso , 'ID_CATEGORIA'=>$idCategoria , 'ID_RONDA'=>5]);
			}

			return $rs;
		}

		/**
		 * Obtiene las preguntas de la categoria y grado de dificultad indicado
		 * @param  integer $categoria 
		 * @param  integer $grado     
		 * @return array            
		 */
		public function getPreguntasByCatGrado($categoria,$grado){
			$sentencia = "SELECT * FROM preguntas WHERE ID_CATEGORIA = ?  AND ID_GRADO =?";
			$values= ['ID_CATEGORIA' => $categoria, 'ID_GRADO'=>$grado];
			return $this->query($sentencia, $values,true);
		}

		/**
		 * Devuelve true si la pregunta ya existe para el concurso y ronda acual
		 * @param  [int] $concurso 
		 * @param  [int] $ronda    
		 * @param  [int] $pregunta 
		 * @return [type]           
		 */
		public function existePreguntaEnConcursoRonda($concurso,$pregunta){
			$values = ['ID_CONCURSO'=>$concurso,'ID_PREGUNTA'=>$pregunta];
			$where = ' ID_CONCURSO=? AND ID_PREGUNTA = ? ';
			return count($this->get($where,$values));
		}

		/**
		 * Obtiene las preguntas generadas para el concurso y la ronda 
		 * @param  [int] $concurso 
		 * @param  [int] $ronda    
		 * @return [assoc array]           
		 */
		public function getPreguntasByConcursoRonda($concurso,$ronda,$categoria,$es_empate = false,$nivel_desempate =0){
			$query = "SELECT pg.PREGUNTA_POSICION,p.PREGUNTA,pg.ID_PREGUNTA,
				pg.ID_GENERADA,pg.LANZADA,pg.HECHA,g.*,c.*
				FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA 
				INNER JOIN grados_dificultad g ON p.ID_GRADO = g.ID_GRADO
				INNER JOIN categorias c ON p.ID_CATEGORIA = c.ID_CATEGORIA
				WHERE ID_RONDA = ? AND ID_CONCURSO = ? AND p.ID_CATEGORIA = ?";
				$values = array('ID_RONDA'=>$ronda,'ID_CONCURSO'=>$concurso,'ID_CATEGORIA'=>$categoria);
				if($es_empate){
					$query .= " AND NIVEL_EMPATE = ?";
					$values['NIVEL_EMPATE'] = $nivel_desempate;
				}
            
			return $this->query($query,$values);
		}

		/**
		 * Genera las preguntas exclusivamente para la 2nda ronda grupal que es distinta al resto
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @return array           
		 */
		public function getPreguntas2nda($concurso,$ronda){
			$query = "SELECT pg.PREGUNTA_POSICION,p.PREGUNTA,pg.ID_PREGUNTA,
				pg.ID_GENERADA,pg.LANZADA,pg.HECHA,g.*,c.*,cr.ID_CONCURSANTE,cr.CONCURSANTE
				FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA 
				INNER JOIN grados_dificultad g ON p.ID_GRADO = g.ID_GRADO
				INNER JOIN categorias c ON p.ID_CATEGORIA = c.ID_CATEGORIA
				INNER JOIN concursantes cr ON pg.ID_CONCURSANTE = cr.ID_CONCURSANTE
				WHERE pg.ID_RONDA = ? AND pg.ID_CONCURSO = ? ORDER BY g.ID_GRADO,pg.OLEADA,cr.CONCURSANTE_POSICION";
            $values = array('ID_RONDA'=>$ronda,'ID_CONCURSO'=>$concurso);
			return $this->query($query,$values);
		}

		/**
		 * Elimina las preguntas generadas
		 * @param  [int] $id     
		 * @param  [string] $where  
		 * @param  [array] $values 
		 * @return [assoc array]         
		 */
		public function eliminar($id,$where,$values){
			return $this->delete($id, $where, $values);
		}

		/**
		 * Devuelve la cantidad de preguntas generadas para la ronda y concurso
		 * @param  [int] $idConcurso 
		 * @param  [int] $idRonda    
		 * @return [assoc_array]             
		 */
		public function cantidadPreguntasTotal($idConcurso,$idRonda){
			$sentencia = 'SELECT COUNT(ID_PREGUNTA) as total FROM preguntas_generadas WHERE ID_CONCURSO = ? AND ID_RONDA = ?';
			$values = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=> $idRonda];

			return $this->query($sentencia , $values, true)[0]['total'];
		}

		/**
		 * Devueve la antidad de preguntas en la ronda y concurso por categoria
		 * @param  [int] $idConcurso  
		 * @param  [int] $idRonda     
		 * @param  [int] $idCategoria 
		 * @return [assoc_array]              
		 */
		public function cantidadPreguntasCategoria($idConcurso,$idRonda,$idCategoria){
			$query = 'SELECT COUNT(preguntas_generadas.ID_PREGUNTA) as total FROM preguntas_generadas LEFT JOIN preguntas ON preguntas_generadas.ID_PREGUNTA = preguntas.ID_PREGUNTA WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CATEGORIA = ?';
			
			$valores = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=> $idRonda, 'ID_CATEGORIA'=>$idCategoria];

			return $this->query($query , $valores, true)[0]['total'];
		}

		/**
		 * Devuelve los cantidades de las preguntas por cada una de las categorias en la ronda y el concurso
		 * @param  [int] $concurso 
		 * @param  [int] $ronda    
		 * @return [assoc_array]           
		 */
		public function getCantidad($concurso,$ronda){
			$query = "SELECT r.RONDA AS ronda ,SUM(CASE WHEN p.ID_CATEGORIA= 1 then 1 else 0 end) geofisica,SUM(CASE WHEN p.ID_CATEGORIA= 2 then 1 else 0 end) geologia,SUM(CASE WHEN p.ID_CATEGORIA= 3 then 1 else 0 end) petroleros,SUM(CASE WHEN p.ID_CATEGORIA= 4 then 1 else 0 end) generales FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA= p.ID_PREGUNTA INNER JOIN rondas r ON pg.ID_RONDA = r.ID_RONDA  WHERE pg.ID_CONCURSO = ? and pg.ID_RONDA = ? ";

			$values = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda];

			return $this->query($query,$values);
		}

		/**
		 * Obtiene la cantidad de preguntas generadas
		 * @param  integer $etapa    
		 * @param  integer $concurso 
		 * @return array           
		 */
		public function getCantidadGeneradas($etapa,$concurso){
			$rs = ['estado'=>0,'mensaje'=>'No se obtuvieron los contadores'];
			try {
				$rondas = new Rondas();
				$rondas = $rondas->getRondas($etapa);
				foreach ($rondas['rondas'] as $ronda) {
					if($ronda['ALIAS'] == 'ind_primer_ronda' || $ronda['ALIAS'] == 'grp_primer_ronda' )
						$rs['contadores'][0] = $this->getCantidad($concurso, $ronda['ID_RONDA'])[0];
					if($ronda['ALIAS'] == 'ind_segunda_ronda' || $ronda['ALIAS'] == 'grp_segunda_ronda' )
						$rs['contadores'][1] = $this->getCantidad($concurso, $ronda['ID_RONDA'])[0];
					if($ronda['ALIAS'] == 'ind_desempate' || $ronda['ALIAS'] == 'grp_desempate' )
						$rs['contadores'][2] = $this->getCantidad($concurso, $ronda['ID_RONDA'])[0];
				}
				//echo json_encode($rs['contadores']);
				$rs['estado']= 1;
				$rs['mensaje']='Contadores obtenidos con exito';

			} catch (Exception $e) {
				$rs['estado']= 0;
				$rs['mensaje']='No se pudieron obtener los contadores';
			}
			
			return $rs;
		}
		/**
		 * Marca como hecha la pregunta y lanza para que aparezca dicha pregunta al concursante
		 * @param  [int] $idGenerada 
		 * @param  [int] $concurso   
		 * @param  [int] $ronda      
		 * @return [assoc array]             
		 */
		public function lanzarPregunta($idGenerada,$concurso,$idRonda,$idCategoria,$es_desempate = false,$nivel_empate = 0){
			//objeto del a ronda 
			$ronda = new Rondas();
			$ronda = $ronda->getRonda($idRonda);
			$regla = new Reglas();
			$regla = $regla->getReglasByRonda($idRonda)[0];
			// la marcamos como hecha
			$values = ['HECHA' => 1];
			if(!$this->update($idGenerada , $values))
				return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			// la ponemos como lanzada para que sea la que aparezca al participante
			$sentencia  = "SELECT pg.* FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ? ";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $idRonda, 'ID_CATEGORIA'=>$idCategoria];
			
			if($es_desempate){
				$sentencia .= ' AND pg.NIVEL_EMPATE= ? ';
				$valores['NIVEL_EMPATE'] = $nivel_empate;
			}

			$sentencia .= ' AND LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1 ';

			$result = $this->query($sentencia, $valores);
			// si no hay lanzadas ponemos la primera en 1
			if(count($result) <= 0){
				$values = ['LANZADA' => 1];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			}else{
				// si ya se lanzaron previamente otras
				$otraSentencia = "SELECT MAX(LANZADA) AS ultima FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ? ";
				if($es_desempate){
					$otraSentencia .= ' AND pg.NIVEL_EMPATE= ? ';
				}
				$rs = $this->query($otraSentencia, $valores);
				$values = ['LANZADA' => ( $rs[0]['ultima'] + 1 )];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];	
			}
			$objRespuesta = new Respuestas();
			$respuestas = $objRespuesta->getRespuestasByPregunta($this->find($idGenerada)['ID_PREGUNTA']);
			return ['estado'=> 1 , 
				'mensaje' => 'Pregunta lanzada con exito, los participantes puedne responder'
				,'respuestas'=>$respuestas];
		}

		/**
		 * Obtiene la ultima pregunta lanzada en la ronda
		 * @param  integer  $concurso        
		 * @param  integer  $ronda           
		 * @param  integer  $categoria       
		 * @param  boolean $es_desempate    
		 * @param  integer $nivel_desempate 
		 * @return array                   
		 */
		public function ultimaLanzada($concurso,$ronda,$categoria,$es_desempate=false,$nivel_desempate = 0){
			$sentencia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ? ";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $ronda];
			if(!$es_desempate){
				$sentencia .= ' AND p.ID_CATEGORIA = ? ';
				$valores['ID_CATEGORIA'] = $categoria;
			}else{
				$sentencia .= " AND pg.NIVEL_EMPATE = ? ";
				$valores['NIVEL_EMPATE'] = $nivel_desempate;
			}
			$sentencia .= " AND pg.LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1 ";
			$result = $this->query($sentencia, $valores);
			return $result;
		}

		/**
		 * Obtiene la ultima pregunta lanzada para el concursante
		 * @param  integer $concurso    
		 * @param  integer $ronda       
		 * @param  integer $categoria   
		 * @param  integer $concursante 
		 * @return array              
		 */
		public function miUltimaLanzada($concurso,$ronda,$categoria,$concursante){
			$response = ['estado'=>0,'mensaje'=>'Sin aaccion'];

			$sentencia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA,pg.TIEMPO_TRANSCURRIDO 
						FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA 
						WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ? AND p.ID_CATEGORIA = ? AND pg.ID_CONCURSANTE = ?";

			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $ronda , 'ID_CATEGORIA'=>$categoria, 'ID_CONCURSANTE'=>$concursante];
			$sentencia .= " AND pg.LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1 ";
			try{
				$result = $this->query($sentencia, $valores);
				if(count($result) > 0){
					unset($valores['ID_CATEGORIA']);
					$whereContestada = "ID_CONCURSO = ? AND ID_RONDA = ?  AND ID_CONCURSANTE = ? ORDER BY ID_TABLERO_PUNTAJE DESC LIMIT 1";
					$tabPuntaje = new TableroPuntaje();
					$registroPuntaje = $tabPuntaje->get($whereContestada , $valores);
					if(count($registroPuntaje) <= 0){
						return ['estado'=> 0, 'mensaje'=>'Vaya parece que tu ultima lanzada no esta asignada en tablero'];
					}
					if($registroPuntaje[0]['CONTESTADA'] == 1){
						return ['estado'=> 0, 'mensaje'=>'Vaya ya has contestado tu ultima pregunta lanzada'];
					}
					$objRespuesta = new Respuestas();
					$respuestas = $objRespuesta->getRespuestasByPregunta($result[0]['ID_PREGUNTA']);
					$response['estado'] = 1;
					$response['pregunta'] = $result;
					$response['pregunta']['respuestas'] = $respuestas;
					$response['mensaje'] = 'Pregunta obtenida exitosamente';
				}else{
					$response['estado'] = 0;
					$response['mensaje'] = "AUN NO LANZAN TU PREGUNTA";
				}
				
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'Fallo al obtener mi ultima pregunta:' . $ex->getMessage() . " Vuelve a intentar";
			}
			
			return $response;
		}

		/**
		 * Verifica que todas las preguntas generadas para el concurso y la ronda esten hechas
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param integer $categoria
		 * @return boolean           
		 */
		public function todasHechas($concurso,$ronda,$categoria,$desempate = false){
			$sentencia = "SELECT pg.* FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ?  AND HECHA  = 0 ";
			$whereValues = ['ID_CONCURSO' => $concurso , 'ID_RONDA' => $ronda];
			if(!$desempate){
				$sentencia .= " AND p.ID_CATEGORIA= ?";
				$whereValues['ID_CATEGORIA'] = $categoria; 
			}
			return count($this->query($sentencia,$whereValues)) <= 0;
		}

		public function guardar($valores){
			return $this->save($valores);
		}

		/**
		 * Genera la informacion de lanzamiento de la pregunta para que este dispotible para ser obtenida, tambien genera el registro
		 * previo en el tablero, para evitar que el concursante no tome por desicion o a tiempo su pregunta y le contbilice su pregunta
		 * asignada
		 * @param  integer $idGenerada    
		 * @param  integer $concurso      
		 * @param  integer $idRonda       
		 * @param  integer $idCategoria   
		 * @param  integer $idConcursante 
		 * @return array   
		 */
		public function lanzarPregunta2nda($idGenerada,$concurso,$idRonda,$idCategoria,$idConcursante){
			// obtenemos la informacion de la pregunta asignada
			$pAsignada = $this->find($idGenerada);
			$ronda = new Rondas();
			$ronda = $ronda->getRonda($idRonda);
			// PRIMERO GENERAMOS EL REGISTRO EN EL TABLERO PARA EL CONCURSANTE
			$tabPuntaje = new TableroPuntaje();
			if($tabPuntaje->preRespuestaPorAsignacion($concurso,$idRonda,$idConcursante,$pAsignada['ID_PREGUNTA']
														,$pAsignada['PREGUNTA_POSICION'],0)['estado'] == 0){
				return ['estado'=> 0 , 'mensaje'=> 'No se pudo generar el tablero de asignacion de pregunta'];
			}
			// la marcamos como hecha
			$values = ['HECHA' => 1];
			if(!$this->update($idGenerada , $values))
				return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			// la ponemos como lanzada para que sea la que aparezca al participante
			$sentencia  = "SELECT pg.* FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ? AND ID_CONCURSANTE = ? AND LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $idRonda, 'ID_CATEGORIA'=>$idCategoria,'ID_CONCURSANTE'=>$idConcursante];
			$result = $this->query($sentencia, $valores);
			// si no hay lanzadas ponemos la primera en 1
			if(count($result) <= 0){
				$values = ['LANZADA' => 1];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0
							,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			}else{
				// si ya se lanzaron previamente otras
				$otraSentencia = "SELECT MAX(LANZADA) AS ultima FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA 
								WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ? AND ID_CONCURSANTE = ? ";
				$rs = $this->query($otraSentencia, $valores);
				$values = ['LANZADA' => ( $rs[0]['ultima'] + 1 )];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0
							,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];	
			}
			$objRespuesta = new Respuestas();
			$respuestas = $objRespuesta->getRespuestasByPregunta($this->find($idGenerada)['ID_PREGUNTA']);
			return ['estado'=> 1 , 
					'mensaje' => 'Pregunta lanzada con exito, los participantes puedne responder',
					'respuestas'=>$respuestas];
		}

		/**
		 * Me devuelve la cantidad de preguntas generadas de la categoria por grado
		 * @param  integer $categoria 
		 * @param  integer $concurso  
		 * @return array            
		 */
		public function preguntasGeneradas($categoria,$concurso){
			$sentencia = 'SELECT "1" grado,COUNT(*) cantidad FROM preguntas_generadas pg LEFT JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA
			WHERE p.ID_CATEGORIA = ? AND ID_GRADO = 1 AND pg.ID_CONCURSO = ?
			UNION
			SELECT "2" grado ,COUNT(*) cantidad FROM preguntas_generadas pg LEFT JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA
			WHERE p.ID_CATEGORIA = ? AND ID_GRADO = 2 AND pg.ID_CONCURSO = ?
			UNION 
			SELECT "3" grado,COUNT(*) cantidad FROM preguntas_generadas pg LEFT JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA
			WHERE p.ID_CATEGORIA = ? AND ID_GRADO = 3 AND pg.ID_CONCURSO = ?';
			$valores = [$categoria,$concurso,$categoria,$concurso,$categoria,$concurso];
			return $this->query($sentencia,$valores);
		}

		/**
		 * Verifica si ya hay  preguntas lanzada para la ronda
		 * @param  integer $ronda        
		 * @param  integer $concurso     
		 * @param  integer $categoria    
		 * @param  integer $nivel_empate 
		 * @return boolean               
		 */
		public function inicioLanzamiento($ronda,$concurso,$nivel_empate){
			$valores = ['ID_CONCURSO'=>$concurso
						,'ID_RONDA'=>$ronda
						,'NIVEL_EMPATE'=>$nivel_empate];
			$where = "ID_CONCURSO = ? AND ID_RONDA = ?  AND NIVEL_EMPATE = ? AND LANZADA > 0";
			$rs = $this->get($where,$valores);
			return count($rs) > 0;
		}

		/**
		 * Obtiene el glosario de preguntas generadas de un concurso
		 * @param integer $concurso
		 */
		public function getGlosarioPreguntas($concurso){
			$queryGlosario = "SELECT pg.PREGUNTA_POSICION numero,p.PREGUNTA pregunta,r.RONDA ronda,pg.NIVEL_EMPATE empate FROM preguntas p INNER JOIN preguntas_generadas pg ON p.ID_PREGUNTA = pg.ID_PREGUNTA
			INNER JOIN concursos c ON c.ID_CONCURSO = pg.ID_CONCURSO
			INNER JOIN rondas r ON r.ID_RONDA = pg.ID_RONDA
			WHERE c.ID_CONCURSO = ? ORDER BY r.ID_RONDA , pg.PREGUNTA_POSICION";

			$valores = ['ID_CONCURSO'=>$concurso];

			return $this->query($queryGlosario,$valores,true);
		}

		public function tieneTodasGeneradas($preguntasNecesarias,$idConcurso, $idRonda,$idCategoria){
			return $this->cantidadPreguntasCategoria($idConcurso, $idRonda, $idCategoria) == $preguntasNecesarias;
		}

	}

	/**
	 * POST REQUESTS
	 */
	if(isset($_POST['functionGeneradas'])){
		$function = $_POST['functionGeneradas'];
		$genera = new PreguntasGeneradas();
		switch ($function) {
			case 'generaPreguntas':
				echo json_encode($genera->generaPreguntas($_POST['ID_CONCURSO'],
					$_POST['ID_CATEGORIA'],$_POST['ID_ETAPA'] ));
				break;
			case 'lanzarPregunta':
				echo json_encode($genera->lanzarPregunta($_POST['ID_GENERADA'] ,$_POST['ID_CONCURSO'] 
					, $_POST['ID_RONDA'],$_POST['ID_CATEGORIA'] , $_POST['IS_DESEMPATE'] , $_POST['NIVEL_EMPATE']));
				break;
			case 'lanzarPregunta2nda':
				echo json_encode($genera->lanzarPregunta2nda($_POST['ID_GENERADA'] ,$_POST['ID_CONCURSO'] 
					, $_POST['ID_RONDA'],$_POST['ID_CATEGORIA'] , $_POST['ID_CONCURSANTE']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida PreguntasGeneradas:POST']);
				break;
		}
	}

	/**
	 * GET REQUESTS
	 */
	
	if(isset($_GET['functionGeneradas'])){
		$function = $_GET['functionGeneradas'];
		$genera = new PreguntasGeneradas();
		switch ($function) {
			case 'miUltimaLanzada':
				echo json_encode($genera->miUltimaLanzada($_GET['ID_CONCURSO'], $_GET['ID_RONDA'], $_GET['ID_CATEGORIA'] , $_GET['ID_CONCURSANTE']));
				break;
			default:
				echo json_encode(['estado'=>0, 'mensaje'=>'Funcion no valida para PreguntasGeneradas:GET']);
		}
	}
	
 ?>