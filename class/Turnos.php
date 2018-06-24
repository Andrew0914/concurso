<?php 
	require_once dirname(__FILE__) .'/database/BaseTable.php';
	require_once dirname(__FILE__) .'/Concursante.php';
	require_once dirname(__FILE__) .'/Rondas.php';

	class Turnos extends BaseTable{

		protected $table = 'turnos';

		public function __construct(){
			parent::__construct();
		}

		public function pasarTurno($idRonda,$idConcurso){
			$concursantes = new Concursante();
			$primerConcursante = $concursantes->getFirst($idConcurso);
			$concursantes= $concursantes->getConcursantes($idConcurso);
			$ronda = new Rondas();
			$ronda = $ronda->getRonda($idRonda);
			$turnosActuales = $this->countTurnos($idConcurso,$idRonda);
			if($ronda['CANTIDAD_PREGUNTAS'] > $turnosActuales){
				if($turnosActuales == 0 ){
					if($this->save(['ID_CONCURSO'=>$idConcurso,
						'ID_RONDA'=>$idRonda,
						'ID_CONCURSANTE'=>$primerConcursante['ID_CONCURSANTE']])){
						return ['estado'=>1,'mensaje'=>'Primer turno asignado', 'ID_CONCURSANTE'=>$primerConcursante['ID_CONCURSANTE']];
					}
				}else{
					if($ronda['TURNOS_PREGUNTA_CONCURSANTE']==1){
						$current_concursante = $this->unTurno($idConcurso,$ronda);
						if($current_concursante > 0 ){
							return ['estado'=>1,'mensaje'=>'Primer turno asignado', 'ID_CONCURSANTE'=>$current_concursante];
						}
					}else if($ronda['TURNOS_PREGUNTA_CONCURSANTE']>1){
						// do something when happen
						return ['estado'=>1,'mensaje'=>'Se asigno turno', 'ID_CONCURSANTE'=>0]; 
					}
				}
			}else{
				return ['estado'=>0,'mensaje'=>'Se terminaron los turnos'];
			}

			return ['estado'=>0,'mensaje'=>'No se pudo establecer el turno'];
		}

		private function unTurno($idConcurso,$ronda){
			$lastTurno = $this->getLast($idConcurso, $ronda['ID_RONDA']);
			$concursante = new Concursante();
			$lastConcursante = $concursante->getLast($idConcurso);
			// SI EL ULTIMO TURNO LO TIENEN EL ULTIMO CONCURSANTE SE REINICIA AL PRIMER CONCURSANTE
			if($lastTurno['ID_CONCURSANTE'] == $lastConcursante['ID_CONCURSANTE']){
				$primerConcursante = $concursante->getFirst($idConcurso);
				if($this->save(['ID_CONCURSO'=>$idConcurso,
							'ID_RONDA'=>$ronda['ID_RONDA'],
							'ID_CONCURSANTE'=>$primerConcursante['ID_CONCURSANTE']])){
					return $primerConcursante['ID_CONCURSANTE'];
				}
			}else{
				$siguientePosicion = $concursante->getConcursante($lastTurno['ID_CONCURSANTE'])['CONCURSANTE_POSICION'] + 1;
				$siguienteConcursante = $concursante->getConcursanteByPosicion($idConcurso,$siguientePosicion);
				if($this->save(['ID_CONCURSO'=>$idConcurso,
							'ID_RONDA'=>$ronda['ID_RONDA'],
							'ID_CONCURSANTE'=>$siguienteConcursante['ID_CONCURSANTE']])){
					return $siguienteConcursante['ID_CONCURSANTE'];
				}
			}

			return 0;
		}

		public function countTurnos($idConcurso,$idRonda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ?";
			$valores = ["ID_CONCURSO"=>$idConcurso,"ID_RONDA"=> $idRonda];
			return count($this->get($where,$valores));
		}

		public function getLast($idConcurso,$idRonda){
			$where = "ID_CONCURSO = ? AND ID_RONDA = ?  ORDER BY ID_TURNO DESC LIMIT 1";
			$valores = ["ID_CONCURSO"=>$idConcurso,"ID_RONDA"=> $idRonda];
			return $this->get($where,$valores)[0];
		}
		
	}
 ?>