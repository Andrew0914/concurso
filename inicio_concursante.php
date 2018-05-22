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
    <body>
        <section class="contenido">
            <h1>Acceder al concurso</h1>
            <form id="form-accede-concurso">
                <select id="ID_CONCURSO" name="ID_CONCURSO" class="form-control">
                    <?php
                    $concurso = new Concurso();
                    $concursosDisponibles = $concurso->getConcursosDisponible();
                    foreach ($concursosDisponibles as $value) {
                        echo '<option value="' . $value['ID_CONCURSO'] . '">' . $value['CONCURSO'] . '</option>';
                    }
                    ?>
                </select>
                <label for="CONCURSANTE">Concursante</label>
                <input type="text" name="CONCURSANTE" id="CONCURSANTE" class="form-control" />
                <label for="PASSWORD">Contraseña</label>
                <input type="text" name="PASSWORD" id="PASSWORD" class="form-control" />
                <br>
                <button class="btn btn-lg btn-primary" type="button" onclick="accederConcurso($('#form-accede-concurso'))">
                    Entrar al concurso
                </button>
            </form>
        </section>
        <!-- INICIO SCRIPTS -->
        <script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/concursante.js"></script>
        <!-- FIN SCRIPTS  -->
    </body>
</html>