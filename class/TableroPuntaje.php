<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Respuestas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/GradoDificultad.php'; 

	class TableroPuntaje extends BaseTable{

		protected $table= 'tablero_puntajes';
		
		public function __construct(){
			parent::__construct();
		}


		public function guardar($data){
			unset($data['functionTablero']);
			$response = ['estado'=> 0 , 'mensaje'=> 'No se genero el tablero correctamente'];
			$respuesta = new Respuestas();
			$generadas = new PreguntasGeneradas();
			try{
				$data['RESPUESTA_CORRECTA'] = 0;
				if($respuesta->esCorrecta($data['PREGUNTA'], $data['RESPUESTA'])){
					$data['RESPUESTA_CORRECTA'] = 1;
				}
				if($data['RESPUESTA'] == 0){
					$data['RESPUESTA'] = null;
				}
				$regla = $generadas->getRegla($data['ID_CONCURSO'],
												$data['ID_RONDA'],
												$data['PREGUNTA']);
				$data['ID_REGLA'] =  $regla['ID_REGLA'];
				$puntaje = $this->save($data);
				if($puntaje > 0){
					$responsePuntaje = $this->generaPuntaje($puntaje,$regla,
						$data['ID_CONCURSANTE'],$data['ID_CONCURSO'],$data['ID_RONDA']);
					$response['estado'] = 1;
					$response['mensaje'] = 'Se genero la informacion en el tablero';
					$response['responsePuntaje'] = $responsePuntaje;	
				}else{
					return $response;
				}
				
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'Fallo el tablero: ' . $ex->getMessage();
			}
			
			return $response;
		}

		public function generaPuntaje($idPuntaje,$regla,$concursante,$concurso,$ronda){
			$grado = new GradoDificultad(); 
			$objPuntaje = $this->find($idPuntaje);
			$puntaje = $grado->getPuntaje($regla['PREGUNTA_DIFICULTAD']);

			// error
			if($objPuntaje['RESPUESTA_CORRECTA'] == 0 AND $regla['RESTA_ERROR'] == 1){
				$puntaje *= -1;
			}else if($objPuntaje['RESPUESTA_CORRECTA'] == 0 AND $regla['RESTA_ERROR'] == 0){
				$puntaje= 0;
			}
			//paso, la segunda condicion es preguntar si el paso ocurrio dentro del turno del concursante
			// $turnos->estoyEnTurni($concutsante,$concurso,$ronda)
			if($objPuntaje['PASO_PREGUNTA'] == 1 AND 1==2){
				$puntaje *= -1;
			}

			$bolUpdate = $this->update($idPuntaje , ['PUNTAJE'=>$puntaje]);
			if($bolUpdate){
				return ['estado'=>1 , 'mensaje'=> 'Se guardo el puntaje'];
			}

			return ['estado'=>0 , 'mensaje'=> 'No se guardo el puntaje'];
		}



	}
	/**
	 * POST REQUESTS
	 */
	
	if(isset($_POST['functionTablero'])){
		$function = $_POST['functionTablero'];
		$tablero = new TableroPuntaje();
		switch ($function) {
			case 'guardar':
				echo json_encode($tablero->guardar($_POST));
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
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO:GET']);
				break;
		}
	}
 ?>
