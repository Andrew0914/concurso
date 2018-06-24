<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php'; 
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	require_once dirname(__FILE__) . '/Turnos.php';

	class PreguntasGeneradas extends BaseTable{

		protected $table = 'preguntas_generadas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Genera las preguntas para la ronda para la cateforia
		 * @param  [int] $idConcurso  
		 * @param  [int] $idRonda     
		 * @param  [int] $idCategoria 
		 * @return boolean              
		 */
		public function generaPreguntas($idConcurso, $idRonda, $idCategoria){
			$ronda = new Rondas();
			$ronda = $ronda->getRonda($idRonda);
			$regla = new Reglas();
			$regla = $regla->getReglasByRonda($idRonda)[0];
			// la cantidad de preguntas por categoria debe considir a la cantidad de grados en el campo
			$grados = explode(',',$regla['GRADOS']);
			$valida= 1;
			if($this->cantidadPreguntasCategoria($idConcurso,$idRonda,$idCategoria) 
				>= $ronda['PREGUNTAS_POR_CATEGORIA']){
				return ['estado'=>0, 
				'mensaje'=> 'Se han completado la cantidad de preguntas para la categoria: '
				.$ronda['PREGUNTAS_POR_CATEGORIA']];
			}
			if($this->cantidadPreguntasTotal($idConcurso,$idRonda) >= $ronda['CANTIDAD_PREGUNTAS']){
				return ['estado'=>0, 
				'mensaje'=> 'Se han completado la cantidad de preguntas para la ronda: '
				.$ronda['CANTIDAD_PREGUNTAS']];
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
			if($valida){
				return ['estado'=> 1,
					'mensaje'=>'Se generaron las preguntas',
					'counts'=> $this->getCantidadGeneradas($idConcurso,$idRonda)];
			}
			return ['estado'=> 0, 'mensaje'=>'NO se generaron las preguntas'];
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
		public function getPreguntasByConcursoRonda($concurso,$ronda){
			$query = "SELECT pg.PREGUNTA_POSICION,p.PREGUNTA,pg.ID_PREGUNTA,pg.ID_GENERADA,pg.LANZADA,pg.HECHA FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE ID_RONDA = ? AND ID_CONCURSO = ? ";
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
		public function getCantidadGeneradas($concurso,$ronda){
			$query = "SELECT SUM(CASE WHEN p.ID_CATEGORIA= 1 then 1 else 0 end) geofisica,SUM(CASE WHEN p.ID_CATEGORIA= 2 then 1 else 0 end) geologia,SUM(CASE WHEN p.ID_CATEGORIA= 3 then 1 else 0 end) petroleros,SUM(CASE WHEN p.ID_CATEGORIA= 4 then 1 else 0 end) generales FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA= p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? and pg.ID_RONDA = ?";

			$values = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda];

			return $this->query($query,$values);
		}

		/**
		 * Marca como hecha la pregunta y lanza para que aparezca dicha pregunta al concursante
		 * @param  [int] $idGenerada 
		 * @param  [int] $concurso   
		 * @param  [int] $ronda      
		 * @return [assoc array]             
		 */
		public function lanzarPregunta($idGenerada,$concurso,$idRonda){
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
			$sentancia  = "SELECT * FROM preguntas_generadas WHERE ID_CONCURSO =? AND ID_RONDA = ? AND LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $idRonda];
			$result = $this->query($sentancia, $valores);
			// si no hay lanzadas ponemos la primera en 1
			if(count($result) <= 0){
				$values = ['LANZADA' => 1];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];
			}else{
				// si ya se lanzaron previamente otras
				$otraSentencia = "SELECT MAX(LANZADA) AS ultima FROM preguntas_generadas WHERE ID_CONCURSO = ? AND ID_RONDA =?";
				$rs = $this->query($otraSentencia, $valores);
				$values = ['LANZADA' => ( $rs[0]['ultima'] + 1 )];
				if(!$this->update($idGenerada , $values))
					return ['estado'=>0,'mensaje' => 'Fallo al lanzar la pregunta, intenta de nuevo'];	
			}
			
			return ['estado'=> 1 , 'mensaje' => 'Pregunta lanzada con exito, los participantes puedne responder'];
		}


		public function ultimaLanzada($concurso,$ronda){
			$sentancia  = "SELECT pg.ID_GENERADA,pg.PREGUNTA_POSICION,pg.LANZADA,p.ID_PREGUNTA,p.PREGUNTA FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA = p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? AND pg.ID_RONDA = ? AND pg.LANZADA != 0 ORDER BY LANZADA DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=> $ronda];
			$result = $this->query($sentancia, $valores);

			return $result;
		}

		/**
		 * Verifica que todas las preguntas generadas para el concurso y la ronda esten hechas
		 * @param  integer $concurso 
		 * @param  integer $ronda    
		 * @return boolean           
		 */
		public function todasHechas($concurso,$ronda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND HECHA  = 0";
			$whereValues = ['ID_CONCURSO' => $concurso , 'ID_RONDA' => $ronda];
			return count($this->get($where,$whereValues)) <= 0;
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
				echo json_encode($genera->generaPreguntas($_POST['ID_CONCURSO'] 
					, $_POST['ID_RONDA'] , $_POST['ID_CATEGORIA'] ));
				break;
			case 'lanzarPregunta':
				echo json_encode($genera->lanzarPregunta($_POST['ID_GENERADA'] ,$_POST['ID_CONCURSO'] 
					, $_POST['ID_RONDA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida PreguntasGeneradas:POST']);
				break;
		}
	}
 ?>