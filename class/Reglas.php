<?php 
	require_once dirname(__FILE__) . '/database/BaseTable.php';

	class Reglas extends BaseTable{

		protected $table = 'reglas';

		public function __construct(){
			parent::__construct();
		}

		/**
		 * Devuelve las reglad para la ronda
		 * @param  int $ronda
		 * @return assoc_array
		 */
		public function getReglasByRonda($ronda){
			$where = 'ID_RONDA = ?';
			$values = array('ID_RONDA'=>$ronda);
			return $this->get($where, $values);
		}


		public function getRegla($id){
			return $this->find($id);
		}

		public function getCountGrados($idRegla){
			$regla = new Reglas();
			$regla = $this->find($idRegla);
			$grados = explode(',',$regla['GRADOS']);
			$rs = [0=>array("grado"=>1,"cantidad"=>0), 1=>array("grado"=>2,"cantidad"=>0),2=>array("grado"=>3,"cantidad"=>0)];
			for($x = 0; $x < count($grados) ; $x++) {
				switch ($grados[$x]) {
					case '1':
						$rs[0]['cantidad'] = $rs[0]['cantidad'] + 1;
						break;
					case '2':
						$rs[1]['cantidad'] = $rs[1]['cantidad'] + 1;
						break;
					case '3':
						$rs[2]['cantidad'] = $rs[2]['cantidad'] + 1;
						break;
				}
			}
			return $rs;
		}

	}

 ?>