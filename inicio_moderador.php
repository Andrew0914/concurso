<!DOCTYPE html>
<html>
    <?php
    require_once 'class/Concurso.php';
    ?>
    <head>
        <meta charset="utf-8">
        <title>Inicio Moderador</title>
        <link rel="shortcut icon" href="image/favicon.png">
        <link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="css/libs/fontawesome/css/all.css"/>
        <link rel="stylesheet" type="text/css" href="css/main.css"/>
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
                <button class="btn btn-lg btn-geo" type="button" onclick="irConcurso()">
                    Entrar al panel
                </button>
                <br>
                <a href="crear" class="enlace">Crear otro concurso</a>
            </form>
        </section>
        <!-- INICIO SCRIPTS -->
        <script type="text/javascript" src="js/libs/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/concurso.js"></script>
        <!-- FIN SCRIPTS  -->
    </body>
</html>