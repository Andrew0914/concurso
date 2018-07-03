<?php
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';
	require_once dirname(__FILE__) . '/Categorias.php';
	require_once dirname(__FILE__) . '/util/Sesion.php';
	require_once dirname(__FILE__) . '/util/SessionKey.php';

	class RondasLog extends BaseTable{

		protected $table = 'rondas_log';

		public function __construct(){
			parent::__construct();
		}

		public function guardar($log){
			return $this->save($log);
		}

		public function getRondasDisponibles($concurso,$rondaActual,$etapa){
			$query = "SELECT * FROM rondas WHERE ID_RONDA != ? AND ID_ETAPA= ? AND ID_RONDA NOT IN(SELECT ID_RONDA FROM rondas_log WHERE ID_CONCURSO = ?)";
			$values = ['ID_RONDA'=>$rondaActual , 'ID_ETAPA'=> $etapa , 'ID_CONCURSO'=>$concurso];
			$response = ['estado'=>0, 'mensaje'=> 'No se pudieron obtener las rondas'];
			try {
				$response['rondas'] = $this->query($query, $values, true);
				$response['estado']= 1;
				$response['mensaje']= 'Rondas obteneidas';
			} catch (Exception $e) {
				$response['estado']=0 ;
				$response['mensaje']= "RondasLog getRondasDisponibles:".$ex->getMessage();
			}

			return $response;
		}

		/**
		 * Finaliza la ronda para la categoria establecida
		 * @param  integer $concurso  
		 * @param  integer $ronda     
		 * @param  integer $categoria 
		 * @return array 
		 */
		public function finalizarRondaCategoria($concurso,$ronda,$categoria){
			$values = ['FIN'=>1];
			$where = "ID_CONCURSO = ? AND ID_RONDA= ? AND ID_CATEGORIA = ?";
			$whereValues = ['ID_CONCURSO'=> $concurso , 'ID_RONDA'=> $ronda , 'ID_CATEGORIA'=>$categoria];
			if($this->update(0, $values, $where, $whereValues))
				return ['estado'=> 1 , 'mensaje'=> 'Ronda finalizada con exito'];
			return ['estado'=> 0 , 'mensaje'=> 'No se pudo finalizar la ronda'];
		}

	
		/**
		 * Elimina un log
		 * @param  integer $id     
		 * @param  string $where  
		 * @param  array $values 
		 * @return boolean         
		 */
		public function eliminar($id,$where,$values){
			return $this->delete($id, $where, $values);
		}

		/**
		 * Pasa a la siguiente ronda normal no desempate si aun hay otra mas
		 * @param  integer $idConcurso 
		 * @param  integer $categoria  
		 * @return array             
		 */
		public function siguienteRonda($idConcurso,$categoria,$rondaActual){
			if(!$this->finalizarRondaCategoria($idConcurso,$rondaActual,$categoria)['estado']){
				return ['estado'=>0, 'mensaje'=>'No se pudo finalizar la ronda actual'];
			}
			$concurso = new Concurso();
			$where = "ID_CONCURSO = ? AND ID_CATEGORIA = ?";
			$valores = ['ID_CONCURSO'=>$idConcurso, 'ID_CATEGORIA'=>$categoria];
			$logs = $this->get($where,$valores);
			$todasFinalizadas = 0;
			foreach ($logs as $log) {
				if($log['FIN'] == 0){
					//actualizamos el cambio de ronda
					if(!$concurso->actualiza($idConcurso,
						['ID_CATEGORIA'=>$categoria,'ID_RONDA'=>$log['ID_RONDA']],
						'ID_CONCURSO=?',
						['ID_CONCURSO'=>$idConcurso])){
						return ['estado'=>0,'mensaje'=>'No se pudo establecer el cambio de ronda'];
					}
					$sesion = new Sesion();
					$sesion->setMany([SessionKey::ID_RONDA=>$log['ID_RONDA'],
									SessionKey::ID_CATEGORIA=>$log['ID_CATEGORIA']]);
					return ['estado'=>1,'mensaje'=>'Cambio de ronda exitoso'];
					break;
				}else{
					$todasFinalizadas ++;
				}
			}

			if($todasFinalizadas == 2){
				return ['estado'=>2,'mensaje'=>'Terminaron las rondas para la categoria'];
			}

			return ['estado'=>0,'mensaje'=>'No se cambio la ronda'];
		}

		/**
		 * Verifica si absolutamente todas las rodas(no desempate) para las categorias esten terminadas == acabo concurso normal
		 * @param  integer $idConcurso 
		 * @return boolean             
		 */
		public function rondasTerminadas($idConcurso){
			// obtenemos la etapa del concurso sus rondas y categorias permitidas
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$ronda = new Rondas();
			$rondas = $ronda->getRondas($concurso['ID_ETAPA'])['rondas'];
			$categorias = new Categorias();
			$categorias = $categorias->getCategoriasPermitidas($concurso['ID_ETAPA'])['categorias'];
			$totales = 0;
			$finalizadas = 0;
			//iteramos categorias y ronda de de cada una
			foreach ($categorias as $cat) {
				foreach ($rondas as $ronda) {
					if($ronda['IS_DESEMPATE'] == 0){
						$where = "ID_CONCURSO = ? AND ID_CATEGORIA = ? AND ID_RONDA = ?";
						$valores = ['ID_CONCURSO' => $idConcurso 
									, 'ID_CATEGORIA' => $cat['ID_CATEGORIA']
									, 'ID_RONDA' => $ronda['ID_RONDA']];
						$rs = $this->get($where , $valores);
						// si no arroja resultado para CONCURSO & CATEGORIA & RONDA
						// quiere decir que ni siquiera ha sido lanzada entonces no an terminado las rondas
						if(count($rs) <= 0){
							return false;
						}
						// contador si finalizo
						if($rs[0]['FIN'] == 1){
							$finalizadas++;
						}
						//contabilizad todas
						$totales++;
					}
				}
			}
			return $totales == $finalizadas;
		}

		/**
		 * Verifica que todas las rondas de uan categoria que no son desempate esten finalzadas == acabo categoria
		 * @param  integer $idConcurso  
		 * @param  integer $idCategoria 
		 * @return boolean              
		 */
		public function rondasTerminadasCategoria($idConcurso , $idCategoria){
			// obtenemos la etapa del concurso sus rondas y categorias permitidas
			$concurso = new Concurso();
			$concurso = $concurso->getConcurso($idConcurso);
			$ronda = new Rondas();
			$rondas = $ronda->getRondas($concurso['ID_ETAPA'])['rondas'];
			$totales = 0;
			$finalizadas = 0;
			foreach ($rondas as $ronda) {
				if($ronda['IS_DESEMPATE'] == 0){
					$where = "ID_CONCURSO = ? AND ID_CATEGORIA = ? AND ID_RONDA = ?";
					$valores = ['ID_CONCURSO' => $idConcurso 
								, 'ID_CATEGORIA' => $idCategoria
								, 'ID_RONDA' => $ronda['ID_RONDA']];
					$rs = $this->get($where , $valores);
					// si no arroja resultado para CONCURSO & CATEGORIA & RONDA
					// quiere decir que ni siquiera ha sido lanzada entonces no an terminado las rondas
					if(count($rs) <= 0){
						return false;
					}
					// contador si finalizo
					if($rs[0]['FIN'] == 1){
						$finalizadas++;
					}
					//contabilizad todas
					$totales++;
				}
			}
			
			return $totales == $finalizadas;
		}

		/**
		 * Evaluea si una ronda especifica de uan categoria ya termino
		 * @param  integer $ronda     
		 * @param  integer $categoria 
		 * @param  integer $concurso  
		 * @return boolean            
		 */
		public function rondaTerminada($ronda,$categoria,$concurso){
			$where = "ID_RONDA = ? AND ID_CATEGORIA = ? AND ID_CONCURSO = ?";
			$valores = ['ID_RONDA'=>$ronda ,'ID_CATEGORIA'=>$categoria, 'ID_CONCURSO'=>$concurso ];
			$log = $this->get($where,$valores)[0];
			return $log['FIN'] == 1;
		}
	}
	/**
	 * POST REQUESTS
	 */

	if(isset($_POST['functionRondasLog'])){
		$function = $_POST['functionRondasLog'];
		$log = new RondasLog();
		switch ($function) {
			case 'siguienteRonda':
				echo json_encode($log->siguienteRonda($_POST['ID_CONCURSO'],$_POST['ID_CATEGORIA'],$_POST['rondaActual']));
				break;
			default:
				echo  json_encode(['estado'=>0,'Operacion no valida RondasLog:POST']);
			break;
		}
	}
?>