<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="image/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	
<div class="btn-group" data-toggle="buttons">
      <?php 
        $puntajes = [0=>['ID_CONCURSANTE'=>'219','CONCURSANTE'=>'a','totalPuntos'=>21] ,
                    1=> ['ID_CONCURSANTE'=>'220','CONCURSANTE'=>'aa','totalPuntos'=>23],
                    2=> ['ID_CONCURSANTE'=>'221','CONCURSANTE'=>'aa','totalPuntos'=>20],
                    3=> ['ID_CONCURSANTE'=>'222','CONCURSANTE'=>'aa','totalPuntos'=>2],
                    4=> ['ID_CONCURSANTE'=>'223','CONCURSANTE'=>'aa','totalPuntos'=>13],
                    5=> ['ID_CONCURSANTE'=>'224','CONCURSANTE'=>'aa','totalPuntos'=>10],
                    6=> ['ID_CONCURSANTE'=>'225','CONCURSANTE'=>'aa','totalPuntos'=>22],
                    7=> ['ID_CONCURSANTE'=>'226','CONCURSANTE'=>'aa','totalPuntos'=>10]];
        
        function empatados( $input ) {
          $empatados = array();
          foreach ($input as  $value) {
            foreach ($input as $v) {
              if($value['ID_CONCURSANTE'] != $v['ID_CONCURSANTE'] AND $value['totalPuntos'] == $v['totalPuntos']){
                $empatados[] = $value;
              }
            }
          }
          $empatados = array_unique($empatados, SORT_REGULAR);;
          echo json_encode($empatados);
        } 
   
        
        empatados($puntajes);
      ?>
</div>

</body>
</html>