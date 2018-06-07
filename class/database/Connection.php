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
            // si sucedio un error
            if ($this->connect_errno) {
                echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            }
        }catch(Exception $ex){
            die($ex->getMessage());
        }
        
    }
}
 ?>