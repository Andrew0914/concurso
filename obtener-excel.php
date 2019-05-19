<!DOCTYPE html>
<html>
    <?php
    require_once 'class/Concurso.php';
    ?>
    <head>
        <meta charset="utf-8">
        <title>Resultados</title>
        <link rel="shortcut icon" href="image/favicon.png">
        <link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/libs/fontawesome/css/all.css"/>
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </head>
    <body class="content content-mdx blanco">
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
                            echo '<option value="' . $value['ID_CONCURSO'] . '">' . $value['CONCURSO'] . '</option>';
                        }
                    ?>
                </select>
                <br>
                <button class="btn btn-lg btn-geo" type="button" onclick="getExcel()">
                    Obtener Excel
                </button>
                <br><br>
                <h1>
                     <a href="#" id="obtener" style="display: none;" class="monserrat-bold" onclick='desapareccer(this)'>
                        <u>Descargar</u> <i class="fas fa-file-download"></i>
                </h1>
                <br><br>
                <h5>
                    <a href="moderador"><u>Volver al inicio </u> </a>
                </h5>
            </form>
        </section>
        <!-- INICIO SCRIPTS -->
        <script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" >
            function getExcel(){
                $.ajax({
                    url: 'class/TablerosExcel.php',
                    type: 'GET',
                    dataType: 'html',
                    data: {'functionExcel': 'generarExcel' , 'ID_CONCURSO' : $("#ID_CONCURSO").val()},
                    success:function(response){
                        $("#obtener").attr("href","gen_excel/" + response);
                        $("#obtener").slideDown(300);
                    },error:function(error){
                        alert("NO SE PUDO GENERAR EL EXCEL");
                    }
                }); 
            }

            function desapareccer(enlace){
                $(enlace).slideUp(300);
            }
        </script>
        <!-- FIN SCRIPTS  -->
    </body>
</html>