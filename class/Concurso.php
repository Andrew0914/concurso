<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';
	require_once dirname(__FILE__) . '/util/Response.php';
	require_once dirname(__FILE__) . '/Concursante.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/PreguntasGeneradas.php';
	require_once dirname(__FILE__) . '/Etapas.php';
	require_once dirname(__FILE__) . '/RondasLog.php';
	require_once dirname(__FILE__) . '/Desempate.php';
	require_once dirname(__FILE__) . '/TableroMaster.php';
	require_once dirname(__FILE__) . '/TableroPaso.php';
	require_once dirname(__FILE__) . '/TableroPosiciones.php';
	require_once dirname(__FILE__) . '/TableroPuntaje.php';

	class Concurso extends BaseTable{

		protected $table = 'concursos';
		private $response;
		public function __construct(){
			parent::__construct();
			$this->response = new Response();
		}

		private function build($values){
			// fecha por defecto
			$datosConcurso = ['FECHA_INICIO' => date('Y-m-d H:i:s') ];
			$objRonda = new Rondas();
			$primerRonda = $objRonda->getPrimeraRonda($values['ID_ETAPA']);
			//valores del nuevo concurso
			$datosConcurso['ID_ETAPA'] = $values['ID_ETAPA'];
			$datosConcurso['CONCURSO'] = $values['CONCURSO'];
			$datosConcurso['ID_CATEGORIA'] = $values['ID_CATEGORIA'];
			$datosConcurso['ID_RONDA']=$primerRonda['ID_RONDA'];

			return $datosConcurso;
		}

		/**
		 * Guarda los datos del concurso en la sesion
		 * @param int $idConcurso
		 * @param array $datosConcurso
		 */
		private function setConcursoSesion($idConcurso , $datosConcurso){
			$sesion = new Sesion();
			$objEtapa = new Etapas();
			$objRonda = new Rondas();
			
			$sessionValues = [SessionKey::ID_CONCURSO => $idConcurso ,
						SessionKey::CONCURSO => $datosConcurso['CONCURSO'],
						SessionKey::ID_ETAPA => $datosConcurso['ID_ETAPA'],
						SessionKey::ETAPA => $objEtapa->getEtapa($datosConcurso['ID_ETAPA']),
						SessionKey::ID_CATEGORIA => $datosConcurso['ID_CATEGORIA'],
						SessionKey::ID_RONDA => $datosConcurso['ID_RONDA'],
						SessionKey::RONDA => $objRonda->getRonda($datosConcurso['ID_RONDA'])];

			$sesion->setMany($sessionValues);
		}

		/**
		 * Genera el concurso  y los concursantes , asi como la asociacion entre estos
		 * @param  [assoc_array] $values [post]
		 * @return [assoc_array]         [arreglo asociativo con data]
		 */
		public function generaConcurso($values){
			$datosConcurso = $this->build($values);
			$idConcursoGuardado = $this->save($datosConcurso);
			$concursante = new Concursante();
			if($idConcursoGuardado == 0 ){
				return $this->response->fail('No se genero el concurso');
			}
			// se crean los concursantes
			for($p=0 ; $p < count($values['CONCURSANTE_POSICION']); $p++) {;
				if($concursante->save(['CONCURSANTE'=>$values['CONCURSANTE'][$p],
					'PASSWORD'=>$values['PASSWORD'][$p],
					'ID_CONCURSO'=>$idConcursoGuardado,
					'CONCURSANTE_POSICION'=>$values['CONCURSANTE_POSICION'][$p]]) == 0){

					$whereDelete = 'ID_CONCURSO = ?';
					$valuesDelete = ['ID_CONCURSO'=>$idConcursoGuardado];
					$concursante->eliminar(0,$whereDelete,$valuesDelete);
					$this->delete(0,$whereDelete,$valuesDelete);
					return $this->fail('NO se generaron los concursantes de manera correcta');
				}
			}

			// genereamos las preguntas para la categoria con la que abren
			$preguntasGeneradas = new PreguntasGeneradas();

			if($preguntasGeneradas->generaPreguntas($idConcursoGuardado,$datosConcurso['ID_CATEGORIA'],$datosConcurso['ID_ETAPA'])['estado'] == 0){
				$whereDelete = 'ID_CONCURSO = ?';
				$valuesDelete = ['ID_CONCURSO'=>$idConcursoGuardado];
				$preguntasGeneradas->eliminar(0,$whereDelete,$valuesDelete);
				$concursante->eliminar(0,$whereDelete,$valuesDelete);
				$this->delete(0,$whereDelete,$valuesDelete);
				return $this->fail('NO se generaron las preguntas iniciales');
			}

			// seteamos los valores del courso creado a la sesion
			$this->setConcursoSesion($idConcursoGuardado , $datosConcurso);
			return ['estado'=>1,'mensaje'=>'CONCURSO CREADO CON EXITO'];
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
				$this->setConcursoSesion($id , $concurso);
				$response['estado'] = 1;
				$response['mensaje'] = 'Acceso al concurso exitoso';
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = 'Acceso al concurso fallido: ' . $ex->getMessage();
			}
			
			return $response;
		}

		/**
		 * Acutlaiza un concurso indicado
		 * @param integer $id
		 * @param array $values
		 * @param string $where
		 * @param array $whereValues
		 */
		public function actualiza($id,$values,$where,$whereValues){
			return $this->update($id,$values,$where,$whereValues);
		}

		/**
		 * Inicia las rondas de preguntas para la categoria elegida
		 * @param  integer $idConcurso  
		 * @param  integer $idCategoria 
		 * @return array              
		 */
		public function iniciarCategoriaRonda($idConcurso,$idCategoria){
			$concurso = $this->find($idConcurso);
			$objRonda = new Rondas();
			$primerRonda = $objRonda->getPrimeraRonda($concurso['ID_ETAPA']);
			$rondas = $objRonda->getRondas($concurso['ID_ETAPA'])['rondas'];
			$generadas = new PreguntasGeneradas();
			$validPreguntasCompletas = 1;
			$valida2 = 1;
			foreach ($rondas as $ronda) {
				if($ronda['ID_RONDA'] == 5){
					$objConcursantes = new Concursante();
					$concursantes = $objConcursantes->getConcursantes($idConcurso)['concursantes'];
					$totales = count($concursantes) * $ronda['TURNOS_PREGUNTA_CONCURSANTE'];
					if($generadas->cantidadPreguntasCategoria($idConcurso, $ronda['ID_RONDA'], $idCategoria) != $totales){
						$valida2 *= 0;
					}
					continue;
				}
				if($ronda['IS_DESEMPATE']==0 AND $ronda['PREGUNTAS_POR_CATEGORIA'] 
					!= $generadas->cantidadPreguntasCategoria($idConcurso, $ronda['ID_RONDA'], $idCategoria)){
					$validPreguntasCompletas *= 0;
				}
			}

			if(!$validPreguntasCompletas and !$valida2){
				return ['estado'=>0,'mensaje'=>'No se puede iniciar con las rondas de esta categoria ya que aun no tiene preguntas'];
			}

			$log = new RondasLog();

			if($log->rondasTerminadasCategoria($idConcurso,$idCategoria)){
				return ['estado'=>0 , 'mensaje' => 'Ya has finalizado las rondas de esta categoria'];
			}

			if(!$this->update($idConcurso,['ID_CATEGORIA'=>$idCategoria , 'ID_RONDA'=> $primerRonda['ID_RONDA'] ])){
				return ['estado'=>0 , 'mensaje'=>'No se establecio la ronda y categoria en el concurso:Table'];
			}

			$validaLog= 1;
			foreach ($rondas as $ronda){
				if($ronda['IS_DESEMPATE'] == 0){
					if(!$log->guardar(['ID_RONDA'=>$ronda['ID_RONDA'] , 
									'ID_CATEGORIA'=>$idCategoria, 
									'ID_CONCURSO'=> $idConcurso,
									'ID_RONDA'=>$ronda['ID_RONDA'],
									'INICIO'=>1,
									'FIN'=>0])){
						$validaLog *= 0;
					}
				}
			}

			if(!$validaLog){
				return ['estado'=>0, 'mensaje'=>'No se pudieron establecer las rondas para la categoria'];
			}

			$sesion = new Sesion();
			$sesion->setOne(SessionKey::ID_CATEGORIA, $idCategoria);
			$sesion->setOne(SessionKey::ID_RONDA, $primerRonda['ID_RONDA']);
			return ['estado'=>1 , 'mensaje'=>'Inicio de categoria exitoso', 'ID_RONDA'=> $primerRonda['ID_RONDA']];
		}
		
		/**
		 * Cierra el concurso
		 * @param integer $concurso
		 */
		public function cerrarConcurso($concurso){
			$sesion = new Sesion();
			$valores = ['FECHA_CIERRE' => date('Y-m-d H:i:s')];
			if($this->update($concurso,$valores)){
				$tabMaster = new TableroMaster();
				if($tabMaster->cerrarTablerosConcurso($concurso)){
					$sesion->kill();
					return ['estado'=>1 , 'mensaje'=>'Concurso finalizado'];
				}
			}

			return ['estado'=>0 ,'mensaje'=>'No se pudo cerrar el concurso'];
		}

		/**
		 * Accede al desempate de la etapa 
		 * @param  integer $idConcurso 
		 * @return array             
		 */
		public function irDesempate($idConcurso,$idTableroMaster){
			$rs = ['estado'=>0 , 'mensaje'=>'No se pudo acceder al desempate'];
			try {
				$concurso = $this->find($idConcurso);
				$nivel_empate = $concurso['NIVEL_EMPATE'] + 1;
				$ronda = new Rondas();
				$desempate = $ronda->getRondaDesempate($concurso['ID_ETAPA']);
				$sesion = new Sesion();
				$sesion->setOne(SessionKey::ID_RONDA , $desempate['ID_RONDA']);
				$objDesempate = new Desempate();
				$genero = $objDesempate->generaPreguntas($concurso['ID_ETAPA'],$concurso['ID_CONCURSO'] , $nivel_empate);
				if($genero['estado'] == 1){
					$log = new RondasLog();
					//guardamos la ronda en el log
					if($log->guardar(['ID_RONDA'=>$desempate['ID_RONDA'] , 'INICIO'=>1 ,'ID_CONCURSO'=>$idConcurso,'ID_CATEGORIA'=>$concurso['ID_CATEGORIA'],'NIVEL_EMPATE'=>$nivel_empate])){
						//actualizamos el concurso a la ronda
						if($this->update($idConcurso,['ID_RONDA'=> $desempate['ID_RONDA'],'NIVEL_EMPATE'=>$nivel_empate] )){
							$rs = ['estado' => 1 , 'mensaje' => 'Accedio al desempate', 'ronda'=>$desempate];
						}
					}
				}
				
			} catch (Exception $e) {
				$rs = ['estado'=>0 , 'mensaje' => $ex->getMessage()];
			}
			return $rs; 
		}

		/**
		 * Verifica si el concurso ha sido cerrado o tiene fecha de cierre
		 * @param integer $concurso 
		 */
		public function concursoCerrado($concurso){
			$cierre = $this->find($concurso)['FECHA_CIERRE'];
			return $cierre != null AND $cierre != '';
		}

		/**
		 * Resetea el concurso, remueve el log de rondas , tableros y genera nuevas preguntas
		 * @param integer $idConcurso
		 */
		public function resetConcurso($idConcurso){
			//Buscamos el concurso a resetear
			$concurso = $this->getConcurso($idConcurso);
			$valida = 1;
			// Reseteamos el concurso a la primer ronda de acuerdo a su etapa
			$objRonda = new Rondas();
			$primerRonda = $objRonda->getPrimeraRonda($concurso['ID_ETAPA']);
			if(!$this->actualiza($concurso['ID_CONCURSO']
			, ['ID_RONDA'=>$primerRonda['ID_RONDA'],'NIVEL_EMPATE'=>0],"",null)){
				return ['estado'=>0 , 'mensaje'=>'No se pudo restablecer la ronda inicial'];
			}
			// eliminamos las rondas de empate del log
			$rondaDesempate = $objRonda->getRondaDesempate($concurso['ID_ETAPA']);
			$rondaLog = new RondasLog();
			if(!$rondaLog->eliminar(0
						,"ID_CONCURSO = ? AND ID_RONDA = ?" 
						, ['ID_CONCURSO'=> $concurso['ID_CONCURSO'] , 'ID_RONDA'=>$rondaDesempate['ID_RONDA']])){
				
				return ['estado'=>0,'mensaje'=>'No se pudo eliminar el avance de rondas'];
			}
			if(!$rondaLog->actualiza(0,['FIN'=>0 , 'NIVEL_EMPATE'=>0],'ID_CONCURSO = ?',['ID_CONCURSO'=>$concurso['ID_CONCURSO']])){
				return ['estado'=>0 , 'mensaje'=>'No se pudieron restablecer los inicios de ronda'];
			}
			// eliminamos los tableros generados para el concurso
			$whereDelete = 'ID_CONCURSO = ?';
			$valuesDelete = ['ID_CONCURSO'=>$concurso['ID_CONCURSO']];
			$tabPuntajes = new TableroPuntaje();
			if(!$tabPuntajes->eliminar(0,$whereDelete,$valuesDelete)){
				return ['estado'=>0, 'mensaje'=>'No se pudieron restablecer los puntajes'];
			}
			$tabPaso = new TableroPaso();
			if(!$tabPaso->eliminar(0,$whereDelete,$valuesDelete)){
				return ['estado'=>0, 'mensaje'=>'No se pudieron restablecer los puntajes de paso'];
			}
			// tablero master borrara sus posicomes generadas ON DELETE CASCADE
			$tabMaster = new TableroMaster();
			if(!$tabMaster->eliminar(0,$whereDelete,$valuesDelete)){
				return ['estado'=>0, 'mensaje'=>'No se pudieron restablecer los tableros'];
			}
			// eliminamos las preguntas generadas y realizamos una nueva aleatorio
			$generar = new PreguntasGeneradas();
			if(!$generar->eliminar(0 ,$whereDelete,$valuesDelete)){
				return ['estado'=>0 , 'mensaje'=> 'No se pudieron eliminar las preguntas previas'];
			}
			//Generamos las preguntas de nuevo
			if($generar->generaPreguntas($concurso['ID_CONCURSO'],$concurso['ID_CATEGORIA'],$concurso['ID_ETAPA'])['estado'] == 0){
				$generar->eliminar(0,$whereDelete,$valuesDelete);
				return ['estado'=>0,'mensaje'=>'NO se generaron las preguntas'];
			}
			// volvemos a permitir a los concursantes entrar
			$concursante = new Concursante();
			if(!$concursante->actualiza(0, ['INICIO_SESION'=>0], $whereDelete , $valuesDelete)){
				return ['estado'=>0 ,'mensaje'=> 'El concurso se reseteo, pero no se reinicio la sesion de los concursantes'];
			}
			return ['estado'=>1,'mensaje'=>'Se ha reseteado el concurso con exito'];
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
			case 'cerrarConcurso';
				echo json_encode($concurso->cerrarConcurso($_POST['ID_CONCURSO']));
				break;
			case 'iniciarCategoriaRonda':
				echo json_encode($concurso->iniciarCategoriaRonda($_POST['ID_CONCURSO'], $_POST['ID_CATEGORIA']));
				break;
			case 'irDesempate':
				echo json_encode($concurso->irDesempate($_POST['ID_CONCURSO'],$_POST['ID_TABLERO_MASTER']));
				break;
			case 'resetConcurso':
				echo json_encode($concurso->resetConcurso($_POST['ID_CONCURSO']));
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