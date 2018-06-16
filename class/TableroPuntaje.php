<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Respuestas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/GradoDificultad.php'; 
	require_once dirname(__FILE__) . '/Concursante.php'; 
	require_once dirname(__FILE__) . '/Reglas.php'; 
	require_once dirname(__FILE__) . '/Preguntas.php'; 

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
		public function preRespuesta($preRespuesta){
			unset($preRespuesta['functionTablero']);
			if($this->save($preRespuesta))
				return ['estado' => 1, 'mensaje'=>'Pre respuesta almacenada con exito'];
			return ['estado' => 0, 'mensaje'=>'No se almaceno la pre respuesta'];
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
			if($correcta == 1){
				$v_puntaje['PUNTAJE'] = $objPregunta->getPuntajeDificultad($pregunta);
			}
			
			if(count($reglas) == 1){
				if($reglas[0]['TIENE_PASO'] == 1 AND $paso == 1 AND $reglas[0]['RESTA_PASO'] ){
					$v_puntaje['PUNTAJE'] *= -1;
				}
				if($correcta == 0 AND $reglas[0]['RESTA_ERROR'] == 1){
					$v_puntaje['PUNTAJE'] *= -1;
				}
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
			$total_concursantes = $concursante->getCountConcursates($concurso)[0]['total'];

			return  $total_concursantes == $contestaron;
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
		public function saveRespuesta($concursante,$concurso,$ronda,$pregunta,$respuesta,$paso = 0){
			$objRespuesta = new Respuestas();
			$valores = ['RESPUESTA' => $respuesta];
			if($objRespuesta->esCorrecta($pregunta, $respuesta)){
				$valores['RESPUESTA_CORRECTA'] = 1;
			}else{
				$valores['RESPUESTA_CORRECTA'] = 0;
			}
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ? ";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA' => $ronda , 'PREGUNTA'=> $pregunta, 'ID_CONCURSANTE' => $concursante];
			if($this->update(0,$valores,$where,$whereValues)){
				if($this->generaPuntaje($concursante, $concurso, $ronda, $pregunta, $respuesta,$valores['RESPUESTA_CORRECTA'],$paso)){
					return ['estado'=>1, 'mensaje'=>'Respuesta almacenada con exito'];
				}
			}

			return ['estado'=>0, 'mensaje'=>'No se almaceno tu respuesta'];
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
				echo json_encode($tablero->saveRespuesta($_POST['ID_CONCURSANTE'],$_POST['ID_CONCURSO'],$_POST['ID_RONDA'],$_POST['ID_PREGUNTA'],$_POST['ID_RESPUESTA']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:POST']);
				break;
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
				if($tablero->todosContestaron($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],$_GET['ID_PREGUNTA'])){
					echo json_encode(['estado'=>1, 'mensaje'=>'Todos contestaron']);
				}else{
					echo json_encode(['estado'=>0, 'mensaje'=>'Aun falta por contestar']);
				}
			break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:GET']);
				break;
		}
	}
 ?>
