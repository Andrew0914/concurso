<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php'; 
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	error_reporting(E_ALL);
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

			$sentencia = "SELECT * FROM preguntas WHERE ID_CATEGORIA = ? ";
			$values= ['ID_CATEGORIA' => $idCategoria];
			for($cont = 1 ; $cont <= $ronda['PREGUNTAS_POR_CATEGORIA']; $cont++){
				$preguntas = $this->query($sentencia,$values,true);
				$key = array_rand($preguntas);
				$preguntaAleatoria = $preguntas[$key];
				while ($this->existePreguntaEnConcursoRonda($idConcurso,$idRonda,
					$preguntaAleatoria['ID_PREGUNTA'])) {
					$preguntas = $this->query($sentencia,$values,true);
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

		public function getPreguntasByConcursoRonda($concurso,$ronda){
			$where = 'ID_RONDA = ? AND ID_CONCURSO = ?';
            $values = array('ID_RONDA'=>$ronda,'ID_CONCURSO'=>$concurso);
			return $this->get($where,$values);
		}

		public function eliminar($id,$where,$values){
			return $this->delete($id, $where, $values);
		}

		public function getRegla($concurso,$ronda,$pregunta){
			$whereClause = "ID_PREGUNTA = ? AND ID_RONDA= ? AND ID_CONCURSO= ?";
			$whereValues= ["ID_PREGUNTA" => $pregunta , "ID_RONDA" => $ronda , "ID_CONCURSO"=> $concurso];
			$objRegla = new Reglas();
			return $objRegla->getRegla($this->get($whereClause , $whereValues)[0]['ID_REGLA']);
		}

		public function cantidadPreguntasTotal($idConcurso,$idRonda){
			$sentencia = 'SELECT COUNT(ID_PREGUNTA) as total FROM preguntas_generadas WHERE ID_CONCURSO = ? AND ID_RONDA = ?';
			$values = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=> $idRonda];

			return $this->query($sentencia , $values, true)[0]['total'];
		}

		public function cantidadPreguntasCategoria($idConcurso,$idRonda,$idCategoria){
			$query = 'SELECT COUNT(preguntas_generadas.ID_PREGUNTA) as total FROM preguntas_generadas LEFT JOIN preguntas ON preguntas_generadas.ID_PREGUNTA = preguntas.ID_PREGUNTA WHERE ID_CONCURSO = ? AND ID_RONDA = ? AND ID_CATEGORIA = ?';
			$valores = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=> $idRonda, 'ID_CATEGORIA'=>$idCategoria];
			return $this->query($query , $valores, true)[0]['total'];
		}

		public function getCantidadGeneradas($concurso,$ronda){
			$query = "SELECT SUM(CASE WHEN p.ID_CATEGORIA= 1 then 1 else 0 end) geofisica,SUM(CASE WHEN p.ID_CATEGORIA= 2 then 1 else 0 end) geologia,SUM(CASE WHEN p.ID_CATEGORIA= 3 then 1 else 0 end) petroleros,SUM(CASE WHEN p.ID_CATEGORIA= 4 then 1 else 0 end) generales FROM preguntas_generadas pg INNER JOIN preguntas p ON pg.ID_PREGUNTA= p.ID_PREGUNTA WHERE pg.ID_CONCURSO = ? and pg.ID_RONDA = ?";

			$values = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda];

			return $this->query($query,$values);
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
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida PreguntasGeneradas:POST']);
				break;
		}
	}
 ?>