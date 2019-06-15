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

				if(!$this->tableroPosiciones->guardaPosiciones( $this->getPosicionesCalculadas($posicionesSegunPuntajes) , $id_master ))
					return $this->response->fail("No se calcularon las posiciones");
				
			}else{

				for($index = 0 ; $index < count($posicionesSegunPuntajes) ; $index++){
					$posicionesSegunPuntajes[$index]['lugar'] = $this->tableroPosiciones->getPosicionPrevia($posicionesSegunPuntajes[$index]['ID_CONCURSANTE'], $concurso,$id_master);
				}

				$posicionesCalculadas = $this->calcularPosicionesConEmpate($posicionesSegunPuntajes);
				
				// guardamos las que no hayan empatado antes
				if(!$this->tableroPosiciones->guardarNoEmpatados($concurso , $id_master))
					return $this->response->fail("No se pudieron guardar los lugares no empatados");

				if(!$this->tableroPosiciones->guardaPosiciones( $this->getPosicionesCalculadas($posicionesCalculadas) , $id_master ))
					return $this->response->fail("No se calcularon las posiciones con empate");
			}
			
			$this->tableroPosiciones->removerEmpateLugaresInferiores($id_master);

			if( !$master->actualiza($id_master ,['POSICIONES_GENERADAS' => 1]) ) 
				return $this->response->fail('No se pudo establecer la bandera de posiciones generadas');

			return $this->response->success(['tablero_master'=>$id_master] , 'Tableros generados');
        }


        private function getPosicionesCalculadas($posicionesSegunPuntajes){
			$positionControl = 0 ;

			// Calculo y asignacion de posiciones y empates
			while($positionControl < (count($posicionesSegunPuntajes) - 1 ) ) {
				for($index = ( $positionControl + 1) ;  $index <= count($posicionesSegunPuntajes) - 1 ; $index++){
					if($posicionesSegunPuntajes[$positionControl]['totalPuntos'] == $posicionesSegunPuntajes[$index]['totalPuntos']){
						$posicionesSegunPuntajes[$index]['lugar'] = $posicionesSegunPuntajes[$positionControl]['lugar'];
						$posicionesSegunPuntajes[$index]['empatado'] = 1;
						$posicionesSegunPuntajes[$positionControl]['empatado'] = 1;
					}
				}
				$positionControl = $positionControl +  1;
			}

			usort($posicionesSegunPuntajes,array($this,"cmpLugar"));

			return $posicionesSegunPuntajes;
        }
        
        private function filtrarPrimerosLugares($lugares){
			$lugaresFiltrados = array();
			foreach($lugares as $lugar){
				if($lugar['lugar'] < 4)
					$lugaresFiltrados[] = $lugar;
			}
			return $lugaresFiltrados;
        }
        
        
        private function calcularPosicionesConEmpate($posiciones){
			$posicionesCalculadas= array();
			$firstPlaces = array();
			$secondPlaces = array();
			$thirdPlaces = array();
			
			foreach($posiciones as $posicion){
				switch ($posicion['lugar']) {
				case 1:
					$firstPlaces[] = $posicion;
					break;
				case 2:
					$secondPlaces[] = $posicion;
					break;
				case 3:
					$thirdPlaces[] = $posicion;
					break;
				}
			}
			
			usort($firstPlaces,array($this,"cmpPuntos"));
			usort($secondPlaces,array($this,"cmpPuntos"));
			usort($thirdPlaces,array($this,"cmpPuntos"));

			$posicionesCalculadas  = array_merge( $this->addInterestPositions($firstPlaces), $this->addInterestPositions($secondPlaces), $this->addInterestPositions($thirdPlaces) );

			return $this->getPosicionesCalculadas( $posicionesCalculadas );
		}

		private function addInterestPositions($places){
			$posicionesCalculadas = array();
			for ($num = 0 ; $num < count($places) ; $num++) {
				$places[$num]['lugar'] = $places[$num]['lugar'] + $num;
				$posicionesCalculadas[] = ($places[$num]);
			}

			return $posicionesCalculadas;
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

		private function cmpPuntos($a , $b){	
		    if ($a['totalPuntos'] == $b['totalPuntos']) {
		        return 0;
		    }
		    return ($a['totalPuntos'] > $b['totalPuntos']) ? -1 : 1;
        }
        
    }

?>