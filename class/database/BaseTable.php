<?php
	
	require_once dirname(__FILE__) . '/Connection.php';

	class BaseTable{
		
		protected $table = 'grados_dificultad';
		protected $id_name;
		
		private $connection;
		
		public function __construct(){
			$this->getIDName();
		}

		/**
		 * Obtiene el nombre de la llave primaria de la tabla de implementacion
		 */
		private function getIDName(){
			$this->connection = new Connection();
			$query = "SHOW KEYS FROM ".$this->table ." WHERE Key_name = 'PRIMARY'";
			$statement = $this->connection->prepare($query);
			$statement->execute();
			$results = $statement->get_result()->fetch_array(MYSQLI_ASSOC);
			$this->id_name = $results['Column_name'];	
			$this->connection->close();	
		}
		
		/**
		 * Metodo para realizar un insert en la tabla de implementacion
		 * @param  array $values arreglo con los nombres de las columnas como llave y los valores a insertar
		 * @return boolean
		 */
		protected function save($values){
			$this->connection = new Connection();
			$valores_insertables = "";
			$query = "INSERT INTO " . $this->table . "(" ;
			end($values);
			$ultimo_elemento =  key($values);
			foreach ($values as $key => $value) {
				if($ultimo_elemento != $key){
					$query .= $key . ",";
					$valores_insertables .=  "?,";
				}else{
					$query .= $key;
					$valores_insertables .=  "?";
				}
				
			}
			$query .= ') VALUES ('. $valores_insertables . ')';
			$statement = $this->connection->prepare($query);
			$params = str_replace("?","s",$valores_insertables);
			$params = str_replace(",","",$params);
			array_unshift($values,$params);
			call_user_func_array(array($statement,'bind_param'),$this->refValues($values));
			// si no se hizo el insert devolvemos 0 
			if(!$statement->execute()){
				if($this->connection->error != null){
					die ($this->connection->error);
				}
				return 0;
			}
			$inserted_id =$this->connection->insert_id; 
			$this->connection->close();
			// si se realizo regresamos el id insertado
			return $inserted_id;
		}

		/**
		 * Funcion para convertir los valores de un arreglo en referencias
		 * @param  [array] $arr 
		 * @return [array references]      
		 */
		private function refValues($arr){
		    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
		    {
		        $refs = array();
		        foreach($arr as $key => $value)
		            $refs[$key] = &$arr[$key];
		        return $refs;
		    }
		    return $arr;
		}

		/**
		 * Obtiene la lista de resultados de la tabla de implementacion
		 * @param  string $whereClause clausula where sin la paralabra WHERE
		 * @param  array $values      valores para la clausula where
		 * @return array              resultados
		 */
		protected function get($whereClause = null, $values = null){
			$this->connection = new Connection();
			$query = "SELECT * FROM ". $this->table;
			$statement = null;
			if($whereClause != null AND $values != null){
				$query .= " WHERE " . $whereClause;
				$statement = $this->connection->prepare($query);
				$params = "";
				for($c = 0; $c < count($values) ; $c++) {
			 		$params .= "s";
			 	}
			 	$values = array_values($values);
			 	array_unshift($values,$params);
				call_user_func_array(array($statement,'bind_param'),$this->refValues($values));
			}else{
				$statement = $this->connection->prepare($query);
			}

			if($this->connection->error != null){
				die ($this->connection->error);
			}

			$statement->execute();
			$results = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
			$this->connection->close();
			return $results;
		}

		/**
		 * Devuelve un objeto de la tabla o clase del id indicado
		 * @param  integer $id identificador del elemento
		 * @return array      
		 */
		protected function find($id = 0){
			$this->connection = new Connection();
			$query = "SELECT * FROM " . $this->table . " WHERE " . $this->id_name . " = ?";
			$statement = $this->connection->prepare($query);
			if($this->connection->error != null){
					die ($this->connection->error);
			}
			$statement->bind_param("i",$id);
			$statement->execute();
			$results = $statement->get_result()->fetch_array(MYSQLI_ASSOC);
			$this->connection->close();
			return $results;
		}
		
		/**
		 * Elimina registro(s) basado en el id o una condicion
		 * @param  integer $id          
		 * @param  [string]  $whereClause 
		 * @param  [assoc_array]  $whereValues 
		 * @return boolean             
		 */
		protected function delete($id = 0, $whereClause = null , $whereValues = null){
			$this->connection = new Connection();
			$query = 'DELETE FROM '. $this->table ;
			if($id > 0 AND $whereClause == null){
		 		$query .= " WHERE ".$this->id_name.'= ? ';
		 		$whereValues = [$id];
		 	}else if($id == 0 AND $whereClause != null){
		 		$query .= ' WHERE '.$whereClause;
		 	}else if($id > 0 AND $whereClause != null){
		 		$query .= ' WHERE '.$this->id_name.'='.$id.' AND ' . $whereClause;
		 	}else{
		 		die(json_encode(['estado'=>0,'mensaje'=>'Delete reqiere al menos una condicion para borrar , como el ID del row o una clausala where']));
		 	}
		 	$statement = $this->connection->prepare($query);
		 	$values = array_values($whereValues);
		 	$params ="";
		 	for($c = 0; $c < count($values) ; $c++) {
			 		$params .= "s";
			}
			array_unshift($values,$params);
			call_user_func_array(array($statement,'bind_param'),$this->refValues($values));
		 	if($this->connection->error != null){
				die ($this->connection->error);
			}
			if($this->table == "tablero_puntajes"){
				//echo $query;
			}
			$bolDelete = $statement->execute();
			$this->connection->close();
			return $bolDelete;
		}
		
		/**
		 * Actualiza uno o mas registros basado en el id o condicion
		 * @param  integer $id          
		 * @param  array  $values      
		 * @param  string  $whereClause 
		 * @param  array  $whereValues 
		 * @return boolean
		 */
		protected function update($id = 0, $values=null, $whereClause = null , $whereValues = null){
			$this->connection = new Connection();
			if($values == null){
				die(json_encode(['estado'=>'0','mensaje'=>'Debes ingresar valores para actualizar: 2do parametro de la funcion']));
			}
		 	$query = 'UPDATE '.$this->table . ' SET ';
		 	end($values);
			$ultimo_elemento =  key($values);
		 	foreach ($values as $key => $value) {
		 		if($ultimo_elemento != $key){
					$query .= $key . ' = ?,';
				}else{
					$query .= $key . ' = ?';
				}
		 	}

		 	if($id > 0 AND $whereClause == null){
		 		$query .= " WHERE ".$this->id_name.'= ? ';
		 		$whereValues = [$id];
		 	}else if($id == 0 AND $whereClause != null){
		 		$query .= ' WHERE '.$whereClause;
		 	}else if($id > 0 AND $whereClause != null){
		 		$query .= ' WHERE '.$this->id_name.'='.$id.' AND ' . $whereClause;
		 	}else{
		 		die(json_encode(['estado'=>0,'mensaje'=>'UPDATE reqiere al menos una condicion para borrar , como el ID del row o una clausala where']));
		 	}

		 	$statement = $this->connection->prepare($query);
		 	$whereValues = array_values($whereValues);
		 	$values = array_values($values);
		 	$values = array_merge($values,$whereValues);
		 	
		 	$params ="";
		 	for($c = 0; $c < count($values) ; $c++) {
			 		$params .= "s";
			}
			array_unshift($values,$params);
			call_user_func_array(array($statement,'bind_param'),$this->refValues($values));
		 	if($this->connection->error != null){
				die ($this->connection->error);
			}
		
			$bolUpdate = $statement->execute();
			$this->connection->close();
			return $bolUpdate;
		}	

		/**
		 * Executa un query con el valor dato
		 * @param  [string]  $query    
		 * @param  [assoc_Array]  $values   
		 * @param  boolean $isSelect 
		 * @return             
		 */
		protected function query($query,$values,$isSelect= true){
			$this->connection = new Connection();
			$statement = $this->connection->prepare($query);
			if($this->connection->error != null){
					die ($this->connection->error);
			}
			$params = "";
			for($c = 0; $c < count($values) ; $c++) {
		 		$params .= "s";
		 	}
		 	$values = array_values($values);
		 	array_unshift($values,$params);
			call_user_func_array(array($statement,'bind_param'),$this->refValues($values));
			$bolExecute= $statement->execute();
			if(!$isSelect){
				return $bolExecute;
			}
			$results = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
			$this->connection->close();
			return $results;
		}
	
	}
	/*$bt = new BaseTable();
	$query = "SELECT * FROM concursantes INNER JOIN concursos ON concursantes.ID_CONCURSO = concursos.ID_CONCURSO WHERE concursos.ID_CONCURSO = ?";
	$values = ['ID_CONCURSO' => 60];
	echo json_encode($bt->query($query , $values , true));*/
?>