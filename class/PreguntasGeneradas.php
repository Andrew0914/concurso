<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php'; 
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	require_once dirname(__FILE__) . '/Turnos.php';
	require_once dirname(__FILE__) . '/Respuestas.php';

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
						while ($this->existePreguntaEnConcursoRonda($idConcurso,$idRonda,
							$preguntaAleatoria['ID_PREGUNTA'])) {
							$preguntas = $this->getPreguntasByCatGrado($idCategoria,$grados[$cont - 1]);
							$preguntaAleatoria = array_rand($preguntas);
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
			if($valida){
				if($mensaje ==''){
					$mensaje = "GENERACION DE PREGUNTAS EXITOSA !";
				}

				$rs = ['estado'=> 1,
					'mensaje'=>$mensaje,
					'counts'=> $this->getCantidadGeneradas($etapa,$idConcurso)];
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
		private function existePreguntaEnConcursoRonda($concurso,$ronda,$pregunta){
			$values = ['ID_CONCURSO'=>$concurso,'ID_RONDA'=>$ronda,'ID_PREGUNTA'=>$pregunta];
			$where = ' ID_CONCURSO=? AND ID_RONDA = ? AND ID_PREGUNTA = ? ';
			return count($this->get($where,$values));
		}

		/**
		 * Obtiene las preguntas generadas para el concurso y la ronda 
		 * @param  [int] $concurso 
		 * @param  [int] $ronda    
		 * @return [assoc array]           
		 */
		public function getPreguntasByConcursoRonda($concurso,$ronda,$categoria){
			$query = "SELECT pg.PREGUNTA_POSICION,p.PREGUNTA,pg.ID_PREGUNTA,
				pg.ID_GENERADA,pg.LANZADA,pg.HECHA,g.*,c.*
				FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA 
				INNER JOIN grados_dificultad g ON p.ID_GRADO = g.ID_GRADO
				INNER JOIN categorias c ON p.ID_CATEGORIA = c.ID_CATEGORIA
				WHERE ID_RONDA = ? AND ID_CONCURSO = ? AND p.ID_CATEGORIA = ?";
            $values = array('ID_RONDA'=>$ronda,'ID_CONCURSO'=>$concurso,'ID_CATEGORIA'=>$categoria);
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
		public function lanzarPregunta($idGenerada,$concurso,$idRonda,$idCategoria){
			//objeto del a ronda 
			$ronda = new Rondas();
			$ronda = $ronda->getRonda($idRonda);
			$regla = new Reglas();
			$regla = $regla->getReglasByRonda($idRonda)[0];
			// validamos la ronda si es que se establece turnos
			$rsTurno = null;
			if($regla['TIENE_TURNOS']){
				$turnos = new Turnos();
				$rsTurno = $turnos->pasarTurno($idRonda, $concurso);
				if($rsTurno['estado'] == 0)
					return $rsTurno;
			}
			// la marcamos como hecha
			$values = ['HECHA' => 1];
			if(!$this->update($idGenerada , $values))
				return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			// la ponemos como lanzada para que sea la que aparezca al participante
			$sentancia  = "SELECT pg.* FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ? AND LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $idRonda , 'ID_CATEGORIA'=>$idCategoria];
			$result = $this->query($sentancia, $valores);
			// si no hay lanzadas ponemos la primera en 1
			if(count($result) <= 0){
				$values = ['LANZADA' => 1];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			}else{
				// si ya se lanzaron previamente otras
				$otraSentencia = "SELECT MAX(LANZADA) AS ultima FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_CONCURSO =? AND ID_RONDA = ? AND p.ID_CATEGORIA= ?";
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

		public function ultimaLanzada($concurso,$ronda,$categoria){
			$sentancia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ? AND p.ID_CATEGORIA = ? AND pg.LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $ronda, 'ID_CATEGORIA'=>$categoria];
			$result = $this->query($sentancia, $valores);

			return $result;
		}

		/**
		 * Verifica que todas las preguntas generadas para el concurso y la ronda esten hechas
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @param integer $categoria
		 * @return boolean           
		 */
		public function todasHechas($concurso,$ronda,$categoria){
			$sentencia = "SELECT pg.* FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ? AND p.ID_CATEGORIA= ? AND HECHA  = 0";
			$whereValues = ['ID_CONCURSO' => $concurso , 'ID_RONDA' => $ronda, 'ID_CATEGORIA'=>$categoria];
			return count($this->query($sentencia,$whereValues)) <= 0;
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
					, $_POST['ID_RONDA'],$_POST['ID_CATEGORIA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida PreguntasGeneradas:POST']);
				break;
		}
	}
 ?>