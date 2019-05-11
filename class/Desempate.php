<?php 
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	require_once dirname(__FILE__) . '/Categorias.php';

	class Desempate{
		
		private $preguntasGeneradas;

		public function __construct(){
			$this->preguntasGeneradas = new PreguntasGeneradas();
		}

		private function getPreguntaRandomNoEnConcurso($preguntas ,$concurso){
			$preguntaAleatoria = $preguntas[array_rand($preguntas , 1)];
			while ($this->preguntasGeneradas->existePreguntaEnConcursoRonda($concurso['ID_CONCURSO'],
				$preguntaAleatoria['ID_PREGUNTA'])) {
				$preguntaAleatoria = $preguntas[array_rand($preguntas , 1)];
			}
			return $preguntaAleatoria;
		}

		private function insertarPreguntaParaDesempate($preguntaAleatoria, $idConcurso , $idRonda, $nivel_empate ){
			$valoresInsert = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
				, 'ID_CONCURSO' => $idConcurso 
				, 'ID_RONDA' => $idRonda
				, 'NIVEL_EMPATE'=>$nivel_empate
				, 'PREGUNTA_POSICION' => ($this->preguntasGeneradas->cantidadPreguntasTotal($idConcurso,$idRonda) + 1) ];

				return $this->preguntasGeneradas->guardar($valoresInsert);
		}

		/**
		 * Genera las preguntas para las rondas de desempate
		 * @param  integer $etapa      
		 * @param  array $idConcurso 
		 * @return array             
		 */
		public function generaPreguntas($concurso,$rondaDesempate,$nivel_empate = 0){
			$response = new Response();
			$objRegla = new Reglas();
			$regla = $objRegla->getReglasByRonda($rondaDesempate['ID_RONDA'])[0];
			$objCategoria = new Categorias();
			$categoria = $objCategoria->getCategoria($concurso['ID_CATEGORIA']);

			if(!$this->suficientesPreguntas($concurso['ID_CATEGORIA'],$concurso['ID_CONCURSO'],$regla['ID_REGLA'])){
				return $response->fail('Ya no es posible desplegar un desempate,por que las preguntas disponibles son insuficientes');
			}

			// la cantidad de preguntas por categoria debe considir a la cantidad de grados en el campo
			$grados = explode(',',$regla['GRADOS']);
			for($cont = 1 ; $cont <= $rondaDesempate['PREGUNTAS_POR_CATEGORIA']; $cont++){
				$preguntas = $this->preguntasGeneradas->getPreguntasByCategoriaGrado($categoria['ID_CATEGORIA'],$grados[$cont - 1]);
				$preguntaAleatoria = $this->getPreguntaRandomNoEnConcurso($preguntas , $concurso);
				if($preguntaAleatoria['ID_PREGUNTA']  == null || $preguntaAleatoria['ID_PREGUNTA']  == ''){
					$cont -=1;
					continue;
				}
				if(!$this->insertarPreguntaParaDesempate($preguntaAleatoria , $concurso['ID_CONCURSO'] , $rondaDesempate['ID_RONDA'] , $nivel_empate) ){

					$this->preguntasGeneradas->eliminar(0,"ID_CONCURSO=?  AND ID_RONDA = ? AND NIVEL_EMPATE = ?" 
					, ['ID_CONCURSO' => $concurso['ID_CONCURSO']  ,'ID_RONDA'=>$rondaDesempate['ID_RONDA'] , 'NIVEL_EMPATE'=>$nivel_empate]);

					return $response->fail('NO se generaron las preguntas para el desempate correctamente');
				}
			}
			return $response->success([] , 'Preguntas generadas para desempate correctamente');
		}

		private function getCantidadAunDisponibles($idCategoria,$idConcurso , $grado){

			$preguntas = new Preguntas();
			$totalesPreguntaGradosCategoria = $preguntas->preguntasTotalesByGradoCategoria($idCategoria);
			$totalUsadasGradosCategoria = $this->preguntasGeneradas->preguntasGeneradas($idCategoria,$idConcurso);

			$totalGradoDisponibles  = array_filter($totalesPreguntaGradosCategoria, function ($var) use ($grado) {
				return ($var['grado'] == $grado);
			});

			$totalGradoUsadas  = array_filter($totalUsadasGradosCategoria, function ($var) use ($grado) {
				return ($var['grado'] == $grado);
			});

			return $totalGradoDisponibles['cantidad'] - $totalGradoUsadas['cantidad'];
		}

		/**
		 * Hay suficientes preguntas para satisfacer la regla de la ronda 
		 * @param int $categoria
		 * @param int $regla
		 * @param int $categoria
		 */
		public function suficientesPreguntas($categoria , $concurso, $regla){
			$reglas = new Reglas();
			$totalesNecesariosRegla = $reglas->getCountGrados($regla);
			for($grado = 1; $grado == 3 ; $grado++) {
				$totalNecesariasGradoRegla  = array_filter($totalesNecesariosRegla, function ($var) use ($grado) {
					return ($var['grado'] == $grado);
				});
				if($this->getCantidadAunDisponibles($categoria,$concurso,$grado) < $totalNecesariasGradoRegla['cantidad']){
					return false;
				}
			}
			return true;
		}

	}

	

?>