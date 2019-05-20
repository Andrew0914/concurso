/**
 * Inicia las preguntas para la categoria elegida
 * @param  integer categoria 
 * @param  integer concurso  
 * @param  integer etapa     
 */
function iniciarCategoria(categoria, concurso) {
    var ajaxTask = $.ajax({
        url: 'class/Concurso.php',
        type: 'POST',
        dataType: 'json',
        data: { 'ID_CONCURSO': concurso, 'ID_CATEGORIA': categoria, 'functionConcurso': 'iniciarRondasCategoria' },
        success: function(response) {
            if (response.estado == 1) {
                window.location.replace('leer_preguntas');
            } else {
                alert(response.mensaje);
            }
        },
        error: function(xhr, text, error) {
            console.log(error);
            alert("Ocurrio error inesperado: " + text);
        },
        complete: function() {
            ajaxTask = null;
        }
    });
}

function generaTableros(concurso) {
    var isDesempate = document.getElementById("IS_DESEMPATE").value;
    var ajaxTask = $.ajax({
        url: 'class/TableroPosiciones.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'functionTabPosiciones': 'generaPosicionesx',
            'ID_CONCURSO': concurso,
            'IS_DESEMPATE': isDesempate
        },
        beforeSend: function() {
            $("#loading-s").show(300);
        },
        success: function(response) {
            if (response.estado == 1) {
                window.location.replace("tablero?id_master=" + response.tablero_master);
            } else {
                alert("No hemos podido calcular los tableros");
            }
        },
        error: function(error) {
            alert("No se pudieron generar los tableros de puntajes, porfavor intentalo de nuevo");
            console.log(error);
        },
        complete: function() {
            isDesempate = null;
            ajaxTask = null;
        }
    });
}

function fetchConcursantes(idConcurso) {
    $.ajax({
        type: "GET",
        url: "class/Concursante.php",
        data: { "functionConcursante": "getConcursantes", "concurso": idConcurso },
        dataType: "json",
        success: function(response) {
            if (response.estado == 1) {
                $('#tbl-concursantes').slideDown(300);
                var concursantes = response.concursantes;
                var content = "";
                for (var d = 0; d < concursantes.length; d++) {
                    content += "<tr>";
                    content += "<td>" + concursantes[d].CONCURSANTE + "</td>";
                    content += "<td>" + concursantes[d].PASSWORD + "</td>";
                    content += "<td>" + (concursantes[d].INICIO_SESION ? "SI" : "NO") + "</td>";
                    content += "<td><button class='btn btn-sm btn-secondary' onclick='liberarConcursante(" + concursantes[d].ID_CONCURSANTE + ")'>Liberar sesión</button></td>";
                    content += "</tr>";
                }
                content += "";
                $("#tbl-concursantes tbody").html(content);
                content = null;
                concursantes = null;
            } else {
                alert(response.mensaje);
            }
        },
        error: function(error) {
            alert("No se pudieron traer los concursantes :(, recarga la página");
        }
    });
}

function liberarConcursante(idConcursante) {
    if (confirm("¿Estas seguro de cerrar la sesion de este concursante?")) {
        $.ajax({
            type: "POST",
            url: "class/Concursante.php",
            data: { "functionConcursante": "liberarConcursante", "ID_CONCURSANTE": idConcursante },
            dataType: "json",
            success: function(response) {
                if (response.estado == 1) {
                    fetchConcursantes($("#ID_CONCURSO").val());
                } else {
                    alert(response.mensaje);
                }
            },
            error: function(error) {
                console.log(error);
                alert("No se pudo liberar al concursante");
            }
        });
    }
}

$(document).ready(function() {
    fetchConcursantes($("#ID_CONCURSO").val());
});