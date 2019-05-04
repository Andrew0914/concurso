<?php 
    class Response{

        function __construct(){}
        
        public function success($key , $object , $message){
            return [
                $key => $object,
                "mensaje"=>$message,
                "estado" => 1
            ];
        }

        public function fail($message){
            return [
                "estado" => 0,
                "mensaje"=> $message
            ];
        }
    }
?>