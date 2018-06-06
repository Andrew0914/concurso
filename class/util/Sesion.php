
<?php 
	/**
	 * Clase para crear todo lo relacionado a la sesion
	 */
	class Sesion {

		public function __construct(){
			// cadaque se inicialice el objeto iniciamos la sesion si no esta activa
			if (session_status() == PHP_SESSION_NONE) {
			    session_start();
			}
		}

		/**
		 * Pone los valores en la sesion que se indiquen en los parametros
		 * @param [assoc_array] $values [arreglo llave->valor con los valores para lasesion]
		 */
		public function setMany($values){
			foreach ($values as $key => $value) {
				$_SESSION[$key] = $value;
			}
		}

		/**
		 * Pone de a un elemento en la sesion
		 * @param [string] $key   [llave]
		 * @param [object] $value [valor]
		 */
		public function setOne($key,$value){
			$_SESSION[$key] = $value;
		}

		/**
		 * Devuelve el valor del atributo dado de la sesion
		 * @param  [string] $key [llave]
		 * @return [object]     
		 */
		public function getOne($key){
			return $_SESSION[$key];
		}

		/**
		 * Destrulle la sesion actual
		 */
		public function kill(){
			session_destroy();
		}

		public function out(){
			$this->kill();
			header('Location: ../../moderador');
		}

	}
	/**
	 * POST REQUESTS
	 */

	if(isset($_POST['functionSesion'])){
		echo "si entro al post";
		$function = $_POST['functionSesion'];
		$sesion = new Sesion();
		switch ($function) {
			case 'out':
				$sesion->out();
				break;
			default:
				echo json_encode(['estado'=>0,'mensaje'=>'funcion no valida Sesion:POST']);
			break;
		}
	}
 ?>