/**
 * Genera el html para los concursantes indicados del concurso
 */
function generaConcursantes() {
    var cantidad = $("#CANTIDAD_PARTICIPANTES").val();
    if (!isNaN(cantidad) && cantidad != "") {
        var concursantesData = "";
        for (var i = 1; i <= cantidad; i++) {
            concursantesData += "<tr>";
            concursantesData += "<td><input type='text' name='CONCURSANTE[]' class='form-control' placeholder='Concursante'></td>";
            concursantesData += "<td><input type='text' name='PASSWORD[]' class='form-control' placeholder='Password'></td>";
            concursantesData += "<td><input type='numeric' name='CONCURSANTE_POSICION[]' class='form-control' readonly value='" + i + "'></td>";
            concursantesData += "</tr>";
        }
        $("#tbl-concursantes tbody").html(concursantesData);
        $("#btn_generar_concursantes").hide(300);
        $("#btn_generar_concurso").show(400);
        $("#btn-deshacer").show(300);
        concursantesData = null;
    } else {
        alert("Por favor pon una cantidad real de concursantes");
    }
    cantidad = null;
}

/**
 * Deshace la seleccion de cantidad de concursantes
 */
function deshacerConcursantes() {
    $("#btn-deshacer").hide(300);
    $("#tbl-concursantes tbody").html("");
    $("#CANTIDAD_PARTICIPANTES").val("");
    $("#btn_generar_concursantes").show(300);
    $("#btn_generar_concurso").hide(400);
}

function validaConcursantes() {
    var validacion = 1;
    $("input[name='PASSWORD[]']").each(function(index, el) {
        if ($(el).val() == "") {
            validacion *= 0;
        }
    });
    $("input[name='CONCURSANTE[]']").each(function(index, el) {
        if ($(el).val() == "") {
            validacion *= 0;
        }
    });
    return validacion == 1;
}

/**
 * Realiza la peticion para la generacion del concurso y concursantes
 * @param  {form} formulario [objeto del formulario]
 */
function generarConcurso(formulario) {
    if ($("#CONCURSO").val() == "" || $("#ID_ETAPA").val() == "" || $("#ID_CATEGORIA").val() == "" || !validaConcursantes()) {
        validaConcursantes();
        alert("Es necesario que ingreses todos los datos del concurso: Nombre,Etapa,Categoria y Concursantes(nombre y password)");
    } else {
        var ajaxTask = $.ajax({
            type: 'POST',
            url: 'class/Concurso.php',
            data: $(formulario).serialize() + "&functionConcurso=generaConcurso",
            dataType: "json",
            beforeSend: function() {
                $("#loading-s").show(300);
            },
            success: function(response) {
                if (response.estado == 1) {
                    window.location.replace("panel");
                } else {
                    alert(response.mensaje);
                    window.location.replace("crear");
                }
            },
            error: function(error) {
                alert("Oops! ocurrio un error inesperado, actualiza la pagina e intenta de nuevo");
                console.log(error);
            },
            complete: function() {
                ajaxTask = null;
            }
        });
    }
}


/**
 * Es el inicio de sesion del moderador para entrar al panel del concurso indicado
 */
function irConcurso() {
    var concurso = $("#ID_CONCURSO").val();
    if (concurso != "") {
        var ajaxTask = $.ajax({
            type: "GET",
            url: "class/Concurso.php",
            data: {
                'functionConcurso': 'irConcurso',
                'concurso': concurso
            },
            dataType: "json",
            success: function(data) {
                if (data.estado == 1) {
                    window.location.replace("panel");
                }
            },
            error: function(error) {
                console.log(error);
            },
            complete: function() {
                ajaxTask = null;
                concurso = null;
            }
        });
    } else {
        alert("Debes seleccionar un concurso");
    }
}

/**
 * Setea las rondas en el select por etapa
 * @param {select} etapa 
 */
function setRondas(etapa) {
    var idEtapa = $(etapa).val();
    var ajaxTask = $.ajax({
        type: "method",
        url: "class/Rondas.php",
        data: {
            'etapa': idEtapa,
            'functionRonda': 'getRondas'
        },
        dataType: "json",
        success: function(data) {
            var rondas = data.rondas;
            var content = "<option value=''>Selecciona una ronda</option>";
            for (var d = 0; d < rondas.length; d++) {
                content += "<option value='" + rondas[d].ID_RONDA;
                content += "'>" + rondas[d].RONDA + "</option>";
            }
            $("#ID_RONDA").html(content);
            rondas = null;
            content = null;
        },
        error: function(error) {
            console.log(error);
        },
        complete: function() {
            ajaxTask = null;
            idEtapa = null;
        }
    });
}

function setCategorias(etapa) {
    var idEtapa = etapa.value;
    var ajaxTask = $.ajax({
        type: "GET",
        url: "class/Categorias.php",
        data: {
            'ID_ETAPA': idEtapa,
            'functionCategorias': 'getCategoriasPermitidas'
        },
        dataType: "json",
        success: function(data) {
            var categorias = data.categorias;
            var content = "<option value=''>Selecciona una Categoria para iniciar</option>";
            for (var d = 0; d < categorias.length; d++) {
                content += "<option value='" + categorias[d].ID_CATEGORIA;
                content += "'>" + categorias[d].CATEGORIA + "</option>";
            }
            $("#ID_CATEGORIA").html(content);
            categorias = null;
            content = null;
        },
        error: function(a, b, c) {
            console.log(a, b, c);
        },
        complete: function() {
            ajaxTask = null;
            idEtapa = null;
        }
    });
}

function cerrarConcurso(concurso) {
    var ajaxTask = $.ajax({
        url: 'class/Concurso.php',
        type: 'POST',
        dataType: 'json',
        data: { 'ID_CONCURSO': concurso, 'functionConcurso': 'cerrarConcurso' },
        beforeSend: function() {
            $("#loading-s").show(300);
        },
        success: function(response) {
            if (response.estado == 1) {
                window.location.replace('obtener_excel');
            }
        },
        error: function(error) {
            console.log(error);
            alert("No se pudo finalizar el concurso");
        },
        complete: function() {
            ajaxTask = null;
        }
    });
}

function irDesempate(concurso, tableroMaster) {
    var ajaxTask = $.ajax({
        url: 'class/Concurso.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'ID_CONCURSO': concurso,
            'functionConcurso': 'irDesempate',
            'ID_TABLERO_MASTER': tableroMaster
        },
        beforeSend: function() {
            $("#loading-s").show(300);
        },
        success: function(response) {
            if (response.estado == 1) {
                window.location.replace('leer_preguntas');
            } else {
                alert(response.mensaje);
            }
        },
        error: function(error) {
            console.log(error);
            alert("No se pudo acceder al desempate");
        },
        complete: function() {
            ajaxTask = null;
        }
    });
}