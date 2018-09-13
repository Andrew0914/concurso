<!DOCTYPE html>
<html>
    <?php
    require_once 'class/Concurso.php';
    ?>
    <head>
        <meta charset="utf-8">
        <title>Restablecer</title>
        <link rel="shortcut icon" href="image/favicon.png">
        <link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </head>
    <body class="content content-sm blanco">
        <?php include 'menu.php'; ?>
        <section class="centrado">
            <h1>
                <img src="image/logo_geollin.png" />
            </h1>
            <br>
            <h2 class="monserrat-bold">
                <b>Bienvenido</b>
            </h2>
            <br>
            <label for="ID_CONCURSO"><b>Concurso</b></label>
            <form id="form-accede-concurso">
                <select id="ID_CONCURSO" name="ID_CONCURSO" class="select-geo">
                    <option value="">Elige el concurso</option>
                    <?php
                        $concurso = new Concurso();
                        $concursosDisponibles = $concurso->getConcursos();
                        foreach ($concursosDisponibles as $value) {
                            if(!$concurso->concursoCerrado($value['ID_CONCURSO'])){
                                echo '<option value="' . $value['ID_CONCURSO'] . '">' . $value['CONCURSO'] . '</option>';
                            }
                        }
                    ?>
                </select>
                <br>
                <button class="btn btn-lg btn-geo" type="button" onclick="resetDesempate()">
                    Restablecer concurso
                </button>
                <br>
                <img src="image/loading.gif" width="50" height="50" id="loading-s" style="display: none" /> 
                <br>
                <h5>
                    <a href="moderador"><u>Volver al inicio </u> </a>
                </h5>
            </form>
        </section>
        <!-- INICIO SCRIPTS -->
        <script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/ajuste-select.js"></script>
        <script type="text/javascript" >
           function resetDesempate(){
                var idConcurso = $("#ID_CONCURSO").val();
                if(idConcurso != "" && idConcurso != 0){
                    $.ajax({
                        url: 'class/Concurso.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'ID_CONCURSO': idConcurso
                            , 'functionConcurso':'resetConcurso'},
                        beforeSend:function(){
                            $("#loading-s").show(300);
                        },
                        success: function (response) {
                            alert(response.mensaje)
                        },error:function(errorResponse,b,c){
                            console.log(errorResponse.responseText);
                            alert("No hemos podido restablecer el concurso :( , intenta de nuevo");
                        },complete:function(){
                            $("#loading-s").hide(300);
                        }
                    });
                }else{
                    alert("Debes elegir un concurso para restablecer");
                }
            }
        </script>
        <!-- FIN SCRIPTS  -->
    </body>
</html>