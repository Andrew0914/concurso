<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php'; 
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/Preguntas.php';

	class PreguntasGeneradas extends BaseTable{

		protected $table = 'preguntas_generadas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Genera el concjunto de preguntas para el concurso
		 * @param  int $idConcurso
		 * @return boolean         
		 */
		public function generarPreguntasIndividual($idConcurso){
			$pregunta = new Preguntas();
			$categoria = new Categorias();
			$concurso = new Concurso();
			$regla= new Reglas();
			$objRonda = new Rondas();
			// importantes
			$categorias = $categoria->getCategorias();
			$ronda = $objRonda->getRonda( $concurso->getConcurso($idConcurso)['ID_RONDA'] );
			$reglas = $regla->getReglas($ronda['ID_RONDA']);
			$totalFinalPreguntas = 0;
			$posicion = 1;
			// importantes
			$whereDelete = 'ID_CONCURSO = :ID_CONCURSO AND ID_RONDA=:ID_RONDA';
			$valuesDelete = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=>$ronda['ID_RONDA']];

			if(!$this->delete(0,$whereDelete,$valuesDelete)){
				return false;
			}

			
			foreach($categorias as $cat){
				foreach ($reglas as $regla) {
					$preguntas = $pregunta->getPreguntasByCategoriaGrado($cat['ID_CATEGORIA'],$regla['PREGUNTA_DIFICULTAD']);
					$key = array_rand($preguntas);
					$preguntaAleatoria = $preguntas[$key];
					while ($this->existePreguntaEnConcursoRonda($idConcurso,
						$ronda['ID_RONDA'],
						$preguntaAleatoria['ID_PREGUNTA'])) {
						
						$preguntas = $pregunta->getPreguntasByCategoriaGrado($cat['ID_CATEGORIA'],$regla['PREGUNTA_DIFICULTAD']);
						$preguntaAleatoria = array_rand($preguntas);

					}
					$values = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
					, 'ID_CONCURSO' => $idConcurso 
					, 'ID_RONDA' => $ronda['ID_RONDA']
					, 'PREGUNTA_POSICION' => $posicion];

					$this->save($values);
					
					$posicion++;
				}
			}
			

			return $ronda['CANTIDAD_PREGUNTAS'] == $posicion;
			
		}

		/**
		 * Devuelve true si la pregunta ya existe para el concurso y ronda acual
		 * @param  [int] $concurso 
		 * @param  [int] $ronda    
		 * @param  [int] $pregunta 
		 * @return [type]           
		 */
		private function existePreguntaEnConcursoRonda($concurso,$ronda,$pregunta){
			$values = ['ID_RONDA'=>$ronda,'ID_CONCURSO'=>$concurso,'ID_PREGUNTA'=>$pregunta];
			$where = ' ID_CONCURSO=:ID_CONCURSO AND ID_RONDA = :ID_RONDA AND ID_PREGUNTA = :ID_PREGUNTA ';
			return count($this->get($where,$values));
		}

		public function getPreguntasByConcursoRonda($concurso,$ronda){
			$query = "SELECT  preguntas_generadas.ID_PREGUNTA,preguntas.PREGUNTA,categorias.*,grados_dificultad.* FROM preguntas_generadas INNER JOIN preguntas ON preguntas_generadas.ID_PREGUNTA = preguntas.ID_PREGUNTA INNER JOIN categorias ON preguntas.ID_CATEGORIA = categorias.ID_CATEGORIA INNER JOIN grados_dificultad ON preguntas.ID_GRADO = grados_dificultad.ID_GRADO WHERE preguntas_generadas.ID_CONCURSO = :ID_CONCURSO AND preguntas_generadas.ID_RONDA = :ID_RONDA ORDER BY preguntas_generadas.PREGUNTA_POSICION ASC";
			$values = [':ID_CONCURSO'=> $concurso, ':ID_RONDA'=>$ronda];
			return $this->query($query, $values);
		}

	}
 ?>