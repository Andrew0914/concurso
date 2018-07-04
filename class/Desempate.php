<?php 
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Reglas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Categorias.php';

	class Desempate{
		
		public function __construct(){
		}

		public function generaPreguntas($etapa,$idConcurso){
			$rs = ['estado'=> 0, 'mensaje'=>'NO se generaron las preguntas'];
			$mensaje ="";
			$objRonda = new Rondas();
			$genera = new PreguntasGeneradas();
			$objRegla = new Reglas();
			$valida= 1;
			$ronda = $objRonda->getRondaDesempate($etapa);
			$idRonda = $ronda['ID_RONDA'];
			$regla = $objRegla->getReglasByRonda($idRonda)[0];
			$categoria = new Categorias();
			$categorias = $categoria->getCategoriasPermitidas($etapa)['categorias'];
			// la cantidad de preguntas por categoria debe considir a la cantidad de grados en el campo
			$grados = explode(',',$regla['GRADOS']);
			for($cont = 1 ; $cont <= $ronda['PREGUNTAS_POR_CATEGORIA']; $cont++){
				foreach ($categorias as $cat) {
					$preguntas = $genera->getPreguntasByCatGrado($cat['ID_CATEGORIA'],$grados[$cont - 1]);
					$key = array_rand($preguntas);
					$preguntaAleatoria = $preguntas[$key];
					while ($genera->existePreguntaEnConcursoRonda($idConcurso,$idRonda,
						$preguntaAleatoria['ID_PREGUNTA'])) {
						$preguntas = $genera->getPreguntasByCatGrado($cat['ID_CATEGORIA'],$grados[$cont - 1]);
						$preguntaAleatoria = array_rand($preguntas);
					}
					$valoresInsert = ['ID_PREGUNTA' => $preguntaAleatoria['ID_PREGUNTA'] 
					, 'ID_CONCURSO' => $idConcurso 
					, 'ID_RONDA' => $idRonda
					, 'PREGUNTA_POSICION' => ($genera->cantidadPreguntasTotal($idConcurso,$idRonda) + 1) ];
					if($genera->guardar($valoresInsert) <= 0){
						$valida *= 0;
					}	
				}
			}
			if($valida){
				if($mensaje ==''){
					$mensaje = "GENERACION DE PREGUNTAS EXITOSA !";
				}

				$rs = ['estado'=> 1,
					'mensaje'=>$mensaje];
			}

			return $rs;
		}
	}

	$desempate = new Desempate();
	echo json_encode($desempate->generaPreguntas(1,166));
?>