<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	require_once dirname(__FILE__) . '/Concursante.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';

	class Concurso extends BaseTable{

		protected $table = 'concursos';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Genera el concurso  y los concursantes , asi como la asociacion entre estos
		 * @param  [assoc_array] $values [post]
		 * @return [assoc_array]         [arreglo asociativo con data]
		 */
		public function generaConcurso($values){
			$valida = 1;
			$concurso = ['FECHA_INICIO' => date('Y-m-d H:i:s') ]; 
			$concurso['ID_ETAPA'] = $values['ID_ETAPA'];
			$concurso['CONCURSO'] = $values['CONCURSO'];
			$concurso_insertado = $this->save($concurso);
			$concursante = new Concursante();
			if($concurso_insertado == 0 ){
				$valida *= 0;
			}
			for($p=0 ; $p < count($values['CONCURSANTE_POSICION']); $p++) {

				$concursante_insertable = ['CONCURSANTE'=>$values['CONCURSANTE'][$p],
										'PASSWORD'=>$values['PASSWORD'][$p],
										'ID_CONCURSO'=>$concurso_insertado,
										'CONCURSANTE_POSICION'=>$values['CONCURSANTE_POSICION'][$p]];

				$inserto = $concursante->save($concursante_insertable);
				if($inserto == 0){
					$valida *= 0;
				}
			}
			if($valida > 0){
				$sesion = new Sesion();
				$sessionValues = [SessionKey::ID_CONCURSO => $concurso_insertado ,
								SessionKey::CONCURSO => $concurso['CONCURSO'],
								SessionKey::ID_ETAPA => $concurso['ID_ETAPA']];
				$sesion->setMany($sessionValues);
				return ['estado'=>1,'mensaje'=>'se genero el concurso exitosamente'];
			}

			return ['estado'=>0,'mensaje'=>'No se genero el concurso de manera correcta'];
		}

		/**
		 * Inicia el concurso, es decir lo activa para que comiencen a funcionar las preguntas
		 * @param  [int] $id [description]
		 * @return [assoc_array]  
		 */
		public function iniciarConcurso($id){
			$ronda = new Rondas();
			$idRonda = $ronda->getPrimeraRonda($this->find($id)['ID_ETAPA'])['ID_RONDA'];
			$values = ['INICIO_CONCURSO' => 1, 'ID_RONDA' => $idRonda];
			if($this->update($id,$values)){
				$generar = new PreguntasGeneradas();

				if($this->find($id)['ID_ETAPA'] == 1 AND $generar->generarPreguntasIndividual($id)){
					return ['estado'=>0,'mensaje'=>'Nose han generado las preguntas para el concurso, vuelve a intentar']; 
				}
				
				return ['estado'=>1,'mensaje'=>'Se inicio el concurso exitosamente, les han comenzado a salir las preguntas a los concursantes.'];
			}
			return ['estado'=>0,'mensaje'=>'No se ha podido iniciar el concurso.']; 
		}

		/**
		 * Devuelve la lista de concursos disponibles no iniciados
		 * @return [assoc_array]
		 */
		public function getConcursosDisponible(){
			$whereClause = 'INICIO_CONCURSO = :INICIO_CONCURSO';
			$values = ['INICIO_CONCURSO'=>0];
			return $this->get($whereClause,$values);
		}

		public function getConcurso($id){
			return $this->find($id);
		}

	}

	/**
	 * Actions - Acciones para la clase
	 */
	if(isset($_POST['functionConcurso'])){
		$function = $_POST['functionConcurso'];
		$concurso = new Concurso();
		switch ($function) {
			case 'generaConcurso':
				echo json_encode($concurso->generaConcurso($_POST));
				break;
			case 'iniciarConcurso';
				echo json_encode($concurso->iniciarConcurso($_POST['ID_CONCURSO']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSO']);
			break;
		}
	}
 ?>