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
			$whereDelete = 'ID_CONCURSO = ? AND ID_RONDA=?';
			$valuesDelete = ['ID_CONCURSO'=>$idConcurso , 'ID_RONDA'=>$ronda['ID_RONDA']];

			if(!$this->delete(0,$whereDelete,$valuesDelete)){
				return false;
			}
			$valida = 1;
			$posicion_regla = 1;
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
					, 'PREGUNTA_POSICION' => $posicion
					, 'ID_REGLA' => $regla['ID_REGLA']];

					if($this->save($values) <= 0){
						$valida *= 0;
					}
			
					$posicion++;
				}
			}
			
			return $valida;
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

	}
 ?>