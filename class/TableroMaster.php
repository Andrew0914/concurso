<?php 

	require_once dirname(__FILE__) . '/database/BaseTable.php';
	
	class TableroMaster extends BaseTable{

		protected $table='tablero_master';

		public function __construct(){
			parent::__construct();
		}

		public function guardar($datos){
			return $this->save($datos);
		}

		public function getMaster($id){
			return $this->find($id);
		}


		public function actualiza($id,$valores,$where = null,$valoresWhere = null){
			return $this->update($id, $valores, $where, $valoresWhere);
		}
		
		public function cerrarTablero($tablero_master){
			return $this->update($tablero_master,['CERRADO'=>1]);
		}

		public function getTablerosMasters($concurso){
			return $this->get("ID_CONCURSO = ? ORDER BY ID_TABLERO_MASTER ASC" ,['ID_CONCURSO'=>$concurso] , true );
		}

		public function cerrarTablerosConcurso($concurso){
			$where = "ID_CONCURSO = ?";
			$whereValues = ['ID_CONCURSO' => $concurso];
			$valores = ['CERRADO' => 1];
			return $this->update(0,$valores,$where,$whereValues);
		}

		public function getLast($concurso){
			$sentencia = "SELECT * FROM tablero_master WHERE ID_CONCURSO = ? ORDER BY ID_TABLERO_MASTER DESC LIMIT 1";
			$valores = ['ID_CONCURSO'=> $concurso];
			return $this->query($sentencia,$valores,true)[0];
		}
	}	
?>