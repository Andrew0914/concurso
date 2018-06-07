<?php
	
	require_once dirname(__FILE__) . '/Connection.php';

	class BaseTable{
		
		protected $table = '';
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
			$results = $statement->fetchAll();
			$this->id_name = $results[0]['Column_name'];
		}
		
		/**
		 * Metodo para realizar un insert en la tabla de implementacion
		 * @param  [assoc_array] $values [arreglo con los nombres de las columnas como llave y los valores a insertar]
		 * @return [boolean]         [insercion realizada]
		 */
		public function save($values){
			$this->connection = new Connection();
			$valores_insertables = "";
			$query = "INSERT INTO " . $this->table . "(" ;
			end($values);
			$ultimo_elemento =  key($values);
			foreach ($values as $key => $value) {
				if($ultimo_elemento != $key){
					$query .= $key . ",";
					$valores_insertables .=  ":$key,";
				}else{
					$query .= $key;
					$valores_insertables .=  ":$key";
				}
				$values[':'.$key] = $value;
        		unset($values[$key]);
			}
			$query .= ') VALUES ('. $valores_insertables . ')';
			$statement = $this->connection->prepare($query);
			// si no se hizo el insert devolvemos 0 
			if(!$statement->execute($values))
				return 0;
			// si se realizo regresamos el id insertado
			return $this->connection->lastInsertId();
		}
		
		/**
		 * Obtiene la lista de resultados de la tabla de implementacion
		 * @param  [string] $whereClause [clausula where sin la paralabra WHERE]
		 * @param  [assoc_Array] $values      [valores para la clausula where]
		 * @return [assoc_array]              [resultados]
		 */
		protected function get($whereClause = null, $values = null){
			$this->connection = new Connection();
			$query = "SELECT * FROM ". $this->table;
			if($whereClause != null AND $values != null){
				$query .= " WHERE " . $whereClause;
				foreach ($values as $key => $value) {
			 		$values[':'.$key] = $value;
	        		unset($values[$key]);
			 	}
			}

			
			$statement = $this->connection->prepare($query);
			$statement->execute($values);

			return $statement->fetchAll();
		}

		/**
		 * Devuelve un objeto de la tabla de implementacion
		 * @param  integer $id [id de elemento]
		 * @return [object]      
		 */
		protected function find($id = 0){
			$this->connection = new Connection();
			$query = "SELECT * FROM " . $this->table . " WHERE " . $this->id_name . " = :ID";
			$statement = $this->connection->prepare($query);
			$statement->bindParam(":ID",$id,PDO::PARAM_INT);
			$statement->execute();
			$results = $statement->fetchAll();
			return $results[0];
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
			$values = null;
			$query = 'DELETE FROM '. $this->table ;
			if($id > 0 AND $whereClause == null){
		 		$values[':ID'] = $id;
		 		$query .= " WHERE ".$this->id_name.'= :ID ';
		 	}else if($id == 0 AND $whereClause != null){
		 		foreach ($whereValues as $key => $value) {
		 			$whereValues[':'.$key] = $value;
        			unset($whereValues[$key]);
		 		}
		 		$values = $whereValues;
		 		$query .= ' WHERE '.$whereClause;
	
		 	}else if($id > 0 AND $whereClause != null){
		 		$values[':ID'] = $id;
		 		foreach ($whereValues as $key => $value) {
		 			$whereValues[':'.$key] = $value;
        			unset($whereValues[$key]);
		 		}
		 		$values = array_merge($values,$whereValues);
		 		$query .= " WHERE ".$this->id_name.'= :ID AND ' . $whereClause;
		 	}else{
		 		die(json_encode(['estado'=>0,'mensaje'=>'Update reqiere al menos una condicion para actualizar , como el ID del row o una clausala where']));
		 	}
		 	
		 	$statement = $this->connection->prepare($query);

			if(!$statement->execute($values))
				return 0;

			return 1;
		}
		
		/**
		 * Actualiza uno o mas registros basado en el id o condicion
		 * @param  integer $id          [description]
		 * @param  [assoc_array]  $values      [description]
		 * @param  [string]  $whereClause [description]
		 * @param  [assoc_array]  $whereValues [description]
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
					$query .= $key . ' = ' . ':' .$key . ',';
				}else{
					$query .= $key . ' = ' . ':' .$key;
				}
				$values[':'.$key] = $value;
        		unset($values[$key]);
		 	}
		 	
		 	if($id > 0 AND $whereClause == null){
		 		$values[':ID'] = $id;
		 		$query .= " WHERE ".$this->id_name.'= :ID ';
		 	}else if($id == 0 AND $whereClause != null){
		 		foreach ($whereValues as $key => $value) {
		 			$whereValues[':'.$key] = $value;
        			unset($whereValues[$key]);
		 		}
		 		$values = array_merge($values,$whereValues);
		 		$query .= ' WHERE '.$whereClause;
		 	}else if($id > 0 AND $whereClause != null){
		 		$values[':ID'] = $id;
		 		foreach ($whereValues as $key => $value) {
		 			$whereValues[':'.$key] = $value;
        			unset($whereValues[$key]);
		 		}
		 		$values = array_merge($values,$whereValues);
		 		$query .= " WHERE ".$this->id_name.'= :ID AND ' . $whereClause;
		 	}else{
		 		die(json_encode(['estado'=>0,'mensaje'=>'Update reqiere al menos una condicion para actualizar , como el ID del row o una clausala where']));
		 	}

		 	$statement = $this->connection->prepare($query);
			if(!$statement->execute($values))
				return 0;
			return 1;
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
			$bolExecute= $statement->execute($values);
			if(!$isSelect){
				return $bolExecute;
			}
			
			return $statement->fetchAll();
		}
	}
?>