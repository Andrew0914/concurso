<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	require_once dirname(__FILE__) . '/Concursante.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Etapas.php';
	require_once dirname(__FILE__) . '/RondasLog.php';

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
			// fecha por defecto
			$concurso = ['FECHA_INICIO' => date('Y-m-d H:i:s') ]; 
			//valores del formulario
			$concurso['ID_ETAPA'] = $values['ID_ETAPA'];
			$concurso['CONCURSO'] = $values['CONCURSO'];
			$concurso['ID_RONDA'] = $values['ID_RONDA'];
			// obtenemos la primer ronda de la etapa elegida para el concurso
			$concurso_insertado = $this->save($concurso);
			$concursante = new Concursante();
			if($concurso_insertado == 0 ){
				return ['estado'=>0,'mensaje'=>'No se genero el concurso de manera correcta'];
			}
			// se crean los concursantes
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

			if($valida == 0){
				$whereDelete = 'ID_CONCURSO = ?';
				$valuesDelete = ['ID_CONCURSO'=>$concurso_insertado];
				$concursante->eliminar(0,$whereDelete,$valuesDelete);
				$this->delete(0,$whereDelete,$valuesDelete);
				return ['estado'=>0,'mensaje'=>'NO se generaron los concursantes de manera correcta'];
			}

			$rondaLog = new RondasLog();
			$log = ['ID_CONCURSO'=>$concurso_insertado, 'ID_RONDA'=> $concurso['ID_RONDA']];
			if(!$rondaLog->guardar($log)){
				$whereDelete = 'ID_CONCURSO = ?';
				$valuesDelete = ['ID_CONCURSO'=>$concurso_insertado];
				$concursante->eliminar(0,$whereDelete,$valuesDelete);
				$this->delete(0,$whereDelete,$valuesDelete);
				return ['estado'=>0,'mensaje'=>'NO se genero la ronda'];
			}
			
			// seteamos los valores del courso creado a la sesion
			$sesion = new Sesion();
			$sessionValues = [SessionKey::ID_CONCURSO => $concurso_insertado ,
							SessionKey::CONCURSO => $concurso['CONCURSO'],
							SessionKey::ID_ETAPA => $concurso['ID_ETAPA'],
							SessionKey::ID_RONDA =>$concurso['ID_RONDA']];
			$sesion->setMany($sessionValues);

			return ['estado'=>1,'mensaje'=>'Se genero el concurso y concursantes exitosamente'];
		}

		/**
		 * Devuelve la lista de concursos disponibles no iniciados
		 * @return [assoc_array]
		 */
		public function getConcursosDisponible(){
			$whereClause = 'ISNULL(FECHA_CIERRE)';
			return $this->get($whereClause,null);
		}

		/**
		 * Obtiebe la lsita de concursos
		 * @return [type] [description]
		 */
		public function getConcursos(){
			return $this->get();
		}

		/**
		 * Obtiene el concurso del id especificado
		 * @param  [int] $id 
		 * @return [Concurso]     
		 */
		public function getConcurso($id){
			return $this->find($id);
		}

		/**
		 * Inicial asesion y te dirije al tablero del concurso indicado
		 * @param  [int] $id [concurso]
		 * @return [assoc array]     [data del cocnurso e inicio de sesion]
		 */
		public function irConcurso($id){
			$response = ['estado'=>0,'mensaje'=>'No se pudo acceder al concurso'];
			try{
				$concurso = $this->find($id);
				$objEtapa = new Etapas();
				$etapa = $objEtapa->getEtapa($concurso['ID_ETAPA']);
				$objRonda = new Rondas();
				$ronda = $objRonda->getRonda($concurso['ID_RONDA']);
				$sesion = new Sesion();
				$sessionValues = [SessionKey::ID_CONCURSO => $id ,
							SessionKey::CONCURSO => $concurso['CONCURSO'],
							SessionKey::ID_ETAPA => $concurso['ID_ETAPA'],
							SessionKey::ETAPA => $etapa['ETAPA'],
							SessionKey::RONDA => $ronda['RONDA'],
							SessionKey::ID_RONDA => $concurso['ID_RONDA']];
				$sesion->setMany($sessionValues);
				$response['estado'] = 1;
				$response['mensaje'] = 'Acceso al concurso exitoso';
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'Acceso al concurso fallido: ' . $ex->getMessage();
			}
			
			return $response;
		}

		public function actualiza($id,$values,$where,$whereValues){
			return $this->update($id,$values,$where,$whereValues);
		}
	}

	/**
	 * POST REQUEST
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
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida CONCURSO:POST']);
			break;
		}
	}

	/**
	 * GET REQUESTS
	 */

	if(isset($_GET['functionConcurso'])){
		$function = $_GET['functionConcurso'];
		$concurso = new Concurso();
		switch ($function) {
			case 'irConcurso':
				echo json_encode($concurso->irConcurso($_GET['concurso']));
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida Concurso:GET']);
			break;
		}
	}
 ?>