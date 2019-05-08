<?php 
    class Response{

        function __construct(){}
        
        public function success($data, $message){
            $responseArray = [];
            foreach($data as $key => $value ){
                $responseArray[$key] = $value;
            }
            $responseArray['mensaje'] = $message;
            $responseArray['estado'] = 1;
            return $responseArray;
        }

        public function fail($message){
            return [
                "estado" => 0,
                "mensaje"=> $message
            ];
        }
    }
?>