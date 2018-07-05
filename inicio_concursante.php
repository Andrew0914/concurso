<!DOCTYPE html>
<html>
    <?php
    require_once 'class/Concurso.php';
    ?>
    <head>
        <meta charset="utf-8">
        <title>Inicio concursante</title>
        <link rel="shortcut icon" href="image/favicon.png">
        <link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </head>
    <body class="content content-xs blanco">
        <section class="centrado">
            <h1>
                <img src="image/logo_geollin.png" />
            </h1>
            <h2 class="monserrat-bold">
                <b>Bienvenido</b>
            </h2>
            <h4>
                El juego esta por comenzar por favor elige el concurso y el equipo al que perteneces.
            </h4>
            <br>
            <label for="ID_CONCURSO"><b>Concurso</b></label>
            <form id="form-accede-concurso">
                <select id="ID_CONCURSO" name="ID_CONCURSO" class="select-geo"  onchange="setConcursantes(this)">
                    <option value="">Elige el concurso</option>
                    <?php
                        $concurso = new Concurso();
                        $concursosDisponibles = $concurso->getConcursosDisponible();
                        foreach ($concursosDisponibles as $value) {
                            if($value['FECHA_CIERRE'] == ''){
                                echo '<option value="' . $value['ID_CONCURSO'] . '">' . $value['CONCURSO'] . '</option>';
                            }
                        }
                    ?>
                </select>
                <br>
                <label for="CONCURSANTE"><b>Concursante</b></label>
                <select name="CONCURSANTE" id="CONCURSANTE" class="select-geo">
                    <option value="">Elige un concursante</option>
                </select>
                <br>
                <label for="PASSWORD"><b>Contraseña</b></label>
                <input type="text" name="PASSWORD" id="PASSWORD" class="form-control" placeholder="Escribir..." />
                <br>
                <button class="btn btn-lg btn-geo" type="button" onclick="accederConcurso($('#form-accede-concurso'))">
                    Comenzar
                </button>
            </form>
        </section>
        <!-- INICIO SCRIPTS -->
        <script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/ronda.js"></script>
        <script type="text/javascript" src="js/concursante.js"></script>
        <!-- FIN SCRIPTS  -->
    </body>
</html>