
<?
    require_once '../Rondas.php';

    class RondaClump{

        private $idEtapa;
        private $rondas;

        public function __construct($idEtapa){
            $this->idEtapa = $idEtapa;
            $this->rondas = new Rondas();
        }

        public function getPrimerRonda(){
            return $this->rondas->getPrimeraRonda($this->idEtapa);
        }

        public function getRondaDesempate(){
            return $this->rondas->getRondaDesempate($this->idEtapa);
        }

        public function getRondas(){
            return $this->rondas->getRondas($this->idEtapa)['rondas'];
        }
    }

?>