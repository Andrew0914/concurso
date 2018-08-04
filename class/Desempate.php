<?php 
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Preguntas.php';
	require_once dirname(__FILE__) . '/Categorias.php';

	class Desempate{
		
		public function __construct(){
		}

		/**
		 * Genera las preguntas para las rondas de desempate
		 * @param  integer $etapa      
		 * @param  integer $idConcurso 
		 * @return array             
		 */
		public function generaPreguntas($etapa,$idConcurso,$nivel_empate = 0){

			$rs = ['estado'=> 0, 'mensaje'=>'NO se generaron las preguntas para el desempate correctamente'];
			$mensaje ="";
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$objRonda = new Rondas();
			$genera = new PreguntasGeneradas();
			$objRegla = new Reglas();
			$valida= 1;
			$ronda = $objRonda->getRondaDesempate($etapa);
			$idRonda = $ronda['ID_RONDA'];
			$regla = $objRegla->getReglasByRonda($idRonda)[0];
			$categoria = new Categorias();
			$cat = $categoria->getCategoria($concurso['ID_CATEGORIA']);
			if(!$this->suficientesPreguntas($concurso['ID_CATEGORIA'],$idConcurso,$regla['ID_REGLA'])){
				return ['estado'=>0 , 'mensaje'=>'Ya no es posible desplegar un desempate,por que las preguntas disponibles son insuficiones'];
			}
			// la cantidad de preguntas por categoria debe considir a la cantidad de grados en el campo
			$grados = explode(',',$regla['GRADOS']);
			for($cont = 1 ; $cont <= $ronda['PREGUNTAS_POR_CATEGORIA']; $cont++){
				$preguntas = $genera->getPreguntasByCatGrado($cat['ID_CATEGORIA'],$grados[$cont - 1]);
				$key = array_rand($preguntas);
				$preguntaAleatoria = $preguntas[$key];
				while ($genera->existePreguntaEnConcursoRonda($idConcurso,
					$preguntaAleatoria['ID_PREGUNTA'])) {
					$preguntas = $genera->getPreguntasByCatGrado($cat['ID_CATEGORIA'],$grados[$cont - 1]);
					$preguntaAleatoria = array_rand($preguntas);
				}
				if($preguntaAleatoria['ID_PREGUNTA']  == null || $preguntaAleatoria['ID_PREGUNTA']  == ''){
					$cont -=1;
					continue;
				}
				$valoresInsert = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
				, 'ID_CONCURSO' => $idConcurso 
				, 'ID_RONDA' => $idRonda
				, 'NIVEL_EMPATE'=>$nivel_empate
				, 'PREGUNTA_POSICION' => ($genera->cantidadPreguntasTotal($idConcurso,$idRonda) + 1) ];
				if($genera->guardar($valoresInsert) <= 0){
					$valida *= 0;
				}	
			}
			if($valida){
				if($mensaje ==''){
					$mensaje = "GENERACION DE PREGUNTAS EXITOSA !";
				}

				$rs = ['estado'=> 1,
					'mensaje'=>$mensaje];
			}else{
				$genera->eliminar(0,"ID_CONCURSO=?  AND ID_RONDA = ? AND NIVEL_EMPATE = ?" 
					, ['ID_CONCURSO' => $idConcurso  ,'ID_RONDA'=>$ronda['ID_RONDA'] , 'NIVEL_EMPATE'=>$nivel_empate]);
			}

			return $rs;
		}

		public function suficientesPreguntas($categoria , $concurso,$regla){
			$preguntas = new Preguntas();
			$reglas = new Reglas();
			$generadas = new PreguntasGeneradas();
			$disponibles = $preguntas->preguntasTotalesDisponibles($categoria);
			$usadas = $generadas->preguntasGeneradas($categoria,$concurso);
			$necesarias = $reglas->getCountGrados($regla);
			foreach ($disponibles as $d) {
				foreach ($usadas as $u) {
					if($d['grado'] == $u['grado']){
						$aunDisponibles = 0;
						$aunDisponibles = $d['cantidad'] - $u['cantidad'];
						foreach($necesarias as $n){
							if($n['grado'] == $u['grado']){
								if($aunDisponibles < $n['cantidad']){
									return false;
								}
							}
						}
					}
				}
			}

			return true;
		}

	}

	/*$desempate = new Desempate();
	echo json_encode($desempate->generaPreguntas(2,139));*/
?>