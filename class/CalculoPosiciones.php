<?php
    require_once dirname(__FILE__).'/util/Response.php';
    require_once dirname(__FILE__) . '/TableroPaso.php';
    require_once dirname(__FILE__) . '/TableroPuntaje.php';
    require_once dirname(__FILE__) . '/TableroPosiciones.php';
    require_once dirname(__FILE__) . '/TableroMaster.php';

    class CalculoPosiciones{

        private $response;
        private $tableroPosiciones;

        public function __construct(TableroPosiciones $tableroPosiciones ){
            $this->response = new Response();
            $this->tableroPosiciones = $tableroPosiciones;
        }

        private function getMejoresPuntajes($concurso,$es_empate){
			$puntaje = new TableroPuntaje();
			$mejores = $puntaje->getMejoresPuntajes($concurso,$es_empate)['mejores'];
			$objConcurso = new Concurso();
			$objConcurso = $objConcurso->getConcurso($concurso);

			// si es grupal contamos las puntuacioens del paso para medir las posiciones
			$mejores = $this->agregarPuntajesRobaPuntos($concurso , $objConcurso['ID_ETAPA'] , $mejores);
			return $mejores;
		}

		private function agregarPuntajesRobaPuntos($concurso , $etapa , $mejores){
			if($etapa == 2){
				$pasos = new TableroPaso();
				$mejoresPaso = $pasos->getMejores($concurso)['mejores'];
				for($x = 0 ; $x<count($mejores) ; $x++) {
					foreach ($mejoresPaso as $mp) {
						if($mejores[$x]['ID_CONCURSANTE'] == $mp['ID_CONCURSANTE']){
							$mejores[$x]['totalPuntos'] = $mejores[$x]['totalPuntos']  + $mp['totalPuntos'];
						}
					}
				}
				// como se le agregaron puntajes de paso cambian los lugres asi que ordenamos
				usort($mejores,array($this,"cmp"));
				//ajustamos lugares
				for($i =0 ; $i < count($mejores) ; $i++) {
					$mejores[$i]['lugar'] = $i+1;
				}
			}

			return $mejores;
		}

		public function generaPosiciones($concurso,$es_empate = false){

			$master = new TableroMaster();
			$id_master = $master->guardar(['ID_CONCURSO' => $concurso]);

			if($id_master <= 0) 
				return $this->response->fail('No se pudo generar el tablero maestro');

			$posicionesSegunPuntajes = $this->getMejoresPuntajes($concurso , $es_empate);

			if(!$es_empate){
				// cuando no es desempate calculo las posiciones como vienen ordenadas
				$posicionesCalculadas = $this->getPosicionesCalculadas($posicionesSegunPuntajes, false);
		
				if(!$this->tableroPosiciones->guardaPosiciones(  $posicionesCalculadas , $id_master ))
					return $this->response->fail("No se calcularon las posiciones");
				
			}else{

				// coloco las posiciones previas cuando es un desempate
				for($index = 0 ; $index < count($posicionesSegunPuntajes) ; $index++){
					$posicionesSegunPuntajes[$index]['lugar'] = $this->tableroPosiciones->getPosicionPrevia($posicionesSegunPuntajes[$index]['ID_CONCURSANTE'], $concurso,$id_master);
				}

				$posicionesCalculadas = $this->getPosicionesCalculadas($posicionesSegunPuntajes , true);
				
				// guardamos las que no hayan empatado antes
				if(!$this->tableroPosiciones->guardarNoEmpatados($concurso , $id_master))
					return $this->response->fail("No se pudieron guardar los lugares no empatados");

				// guardamos las posiciones 
				if(!$this->tableroPosiciones->guardaPosiciones( $posicionesCalculadas , $id_master ))
					return $this->response->fail("No se calcularon las posiciones con empate");
			}
			
			if(!$this->tableroPosiciones->findAndSetEmpates($id_master))
				return $this->response("No se pudieron colocar los empates");

			if( !$master->actualiza($id_master ,['POSICIONES_GENERADAS' => 1]) ) 
				return $this->response->fail('No se pudo establecer la bandera de posiciones generadas');

			return $this->response->success(['tablero_master'=>$id_master] , 'Tableros generados');
        }


        private function getPosicionesCalculadas($posiciones , $es_empate){

			$positionControl = 0 ;
			$count = 1;

			// Calculo y asignacion de posiciones y empates
			while($positionControl < (count($posiciones) - 1 ) ) {
				for($index = ( $positionControl + 1) ;  $index <= count($posiciones) - 1 ; $index++){

					$initialPosition = $posiciones[$index]['lugar'];

					if( $posiciones[$positionControl]['lugar'] == $posiciones[$index]['lugar'] ){

						if ($posiciones[$positionControl]['totalPuntos'] != $posiciones[$index]['totalPuntos']) {
							if (!$es_empate) {
								$posiciones[$index]['lugar'] = $posiciones[$index]['lugar']+$positionControl;
							} else {
								$posiciones[$index]['lugar'] = $posiciones[$index]['lugar'] + $count;
							}
						}

					} else {
						if ($posiciones[$positionControl]['totalPuntos'] == $posiciones[$index]['totalPuntos']) {
							if (!$es_empate) {
								$posiciones[$index]['lugar'] = $posiciones[$positionControl]['lugar'];
							}
						}
					}
					
					if ($es_empate) {
						if ($initialPosition == $posiciones[$index]['lugar']) {
							$count = $count + 1;
						}
					}
				}

				$positionControl = $positionControl +  1;
			}

			usort($posiciones,array($this,"cmpLugar"));

			return $posiciones;
        }
        
        public function filtrarPrimerosLugares($lugares){
			$lugaresFiltrados = array();
			foreach($lugares as $lugar){
				if($lugar['lugar'] < 4)
					$lugaresFiltrados[] = $lugar;
			}
			return $lugaresFiltrados;
        }
        
        private function cmp($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
		}

		private function cmpLugar($a , $b){	
		    if ($a['lugar'] == $b['lugar']) {
		        return 0;
		    }
		    return ($a['lugar'] > $b['lugar']) ? -1 : 1;
		}
        
    }

?>