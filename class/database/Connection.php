<?php 
	class Connection extends PDO{
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
            $dns = $settings['database']['driver'] .
            ':host=' . $settings['database']['host'] .
            ((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
            ';dbname=' . $settings['database']['schema'];
            //creamos el objeto
            parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
        }catch(Exception $ex){
            die($ex->getMessage());
        }
        
    }
}
 ?>