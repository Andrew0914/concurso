<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';
	require_once dirname(__FILE__) . '/Concurso.php';
	require_once dirname(__FILE__) . '/Rondas.php';

	class TableroPaso extends BaseTable{

		protected $table = 'tablero_pasos';

		public function __construct(){
			parent::__construct();
		}

		public function contestoPaso($concurso,$ronda,$pregunta,$concursante){
			$objConcursante = new Concursante();	
			$where = "ID_CONCURSO = ? AND ID_RONDA = ? AND PREGUNTA = ? AND ID_CONCURSANTE = ?";
			$whereValues = ['ID_CONCURSO'=>$concurso , 'ID_RONDA'=>$ronda , 
							'PREGUNTA'=>$pregunta , 'ID_CONCURSANTE'=> $concursante];
			$rs = $this->get($where,$whereValues);
			if(count($rs) > 0){
				return ['estado' => 1 , 'mensaje'=>'El concursante ha contestado, oprime siguiente para elegir otra pregunta'];
			}
			return ['estado'=> 0 , 'mensaje'=>'Aun no realiza accion el concursante'];
		}

		public function cmp($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
		}

		public function getMejores($concurso){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,sum(t.PUNTAJE) as totalPuntos 
						FROM tablero_pasos as t INNER JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE 
						WHERE t.ID_CONCURSO = ? ";
				$values = [':ID_CONCURSO'=>$concurso];
				$query .= " GROUP BY c.ID_CONCURSANTE,c.CONCURSANTE ORDER BY totalPuntos DESC ";
				$mejores = $this->query($query,$values,true);
				usort($mejores,array($this,"cmp"));
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
				$response['mejores'] = $mejores;
				$response['estado'] = 1;
				$response['mensaje'] = "Se obtuvo el puntaje total";
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = "No se obtuvo el puntaje total:" . $ex->getMessage();
			}
			return $response;
		}

		public function getResultados($concurso){
			$response = ['estado'=>0 , 'mensaje'=>'No se obtuvo el puntaje'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);
			$rondas = new Rondas();
			try{
				$query = "SELECT c.ID_CONCURSANTE,c.CONCURSANTE,t.PREGUNTA_POSICION,p.PREGUNTA,r.INCISO , r.RESPUESTA,t.PASO_PREGUNTA,t.PUNTAJE,r.ES_IMAGEN,ro.RONDA,ca.CATEGORIA FROM tablero_pasos as t LEFT JOIN concursantes as c ON t.ID_CONCURSANTE = c.ID_CONCURSANTE LEFT JOIN preguntas as p ON t.PREGUNTA = p.ID_PREGUNTA LEFT JOIN respuestas as r ON t.RESPUESTA = r.ID_RESPUESTA LEFT JOIN rondas ro ON t.ID_RONDA = ro.ID_RONDA INNER JOIN categorias ca ON p.ID_CATEGORIA = ca.ID_CATEGORIA WHERE t.ID_CONCURSO = ?";
				$values = [':ID_CONCURSO'=>$concurso];
				$tablero = $this->query($query,$values,true);
				$response['tablero'] = $tablero;
				$response['estado'] = 1;
				$response['mensaje'] = "Se obtuvo el puntaje";
			}catch(Exception $ex){
				$response['estado'] = 0;
				$response['mensaje'] = "No se obtuvo el puntaje:" . $ex->getMessage();
			}
			return $response;
		}
	}

	/**
	 * GET REQUESTS
	 */
	if(isset($_GET['functionTablero'])){
		$function = $_GET['functionTablero'];
		$tablero = new TableroPaso();
		switch ($function) {
			case 'contestoPaso':
				echo json_encode($tablero->contestoPaso($_GET['ID_CONCURSO'],$_GET['ID_RONDA'],
						$_GET['PREGUNTA'],$_GET['ID_CONCURSANTE']));
			break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida TABLERO_PASO:GET']);
		}
	}
 ?>