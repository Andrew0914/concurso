<?php 
	class Connection extends mysqli{
    	/**
    	 * Constructor del pdo de conexion
    	 * @param string $file [archivo de configuracion de la conexion BD]
    	 */
        private $file;

        public function __construct($ini = 'mysql.ini'){
            $this->file = dirname(__FILE__) . '/' . $ini;
        	// si no se puede abrir el archivo de configuracion
            if (!$settings = parse_ini_file($this->file, TRUE)) {
            	throw new exception('No se puede abrir el archivo de configuracion de la base de datos ' 
            		. $this->file . '.');
            }
            try{
                // preparamos la conexion
                $host = $settings['database']['host'];
                if(!empty($settings['database']['port'])){
                    $host .= ":" . $settings['database']['port'];
                }
                $usuario = $settings['database']['username'];
                $password = $settings['database']['password'];
                $database = $settings['database']['schema'];
                //creamos el objeto
                parent::__construct($host, $usuario, $password, $database);
                $this->set_charset("utf8");
                // si sucedio un error
                if ($this->connect_errno) {
                    echo "Fallo al conectar a MySQL: (" . $this->connect_errno . ") " . $this->connect_error;
                }
            }catch(Exception $ex){
                die($ex->getMessage());
            }
        }

        /**
         * Valida los casos para la creacion del socket de reutilizacion de la conexion de la base de datos
         * Devuelve el socket creado y validado
         * @return socket 
         */
        private function createSocket(){
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (!is_resource($socket)) {
                die('No se pudo crear elsocket: '. socket_strerror(socket_last_error()) . PHP_EOL);
            }
            if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
                die('No se pudo establecer la opción del socket: '. socket_strerror(socket_last_error()) . PHP_EOL);
            }
            if (!socket_bind($socket, '127.0.0.1', 1223)) {
                die('No se pudo vincular el socket: '. socket_strerror(socket_last_error()) . PHP_EOL);
            }
            $rval = socket_get_option($socket, SOL_SOCKET, SO_REUSEADDR);
            if ($rval === false) {
                die('No se pudo obtener la opción del option: '. socket_strerror(socket_last_error()) . PHP_EOL);
            } else if ($rval !== 0) {
                return $socket;
            }

            die("No se ha podido entrar en ningun caso de la creacion del socket");
        }
    }
?>