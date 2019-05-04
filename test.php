<?php
    require_once dirname(__FILE__).'/class/Categorias.php';

    $categoria = new Categorias();
    echo json_encode($categoria->getCategorias());

?>