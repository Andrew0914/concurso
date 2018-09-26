var timerContesto = null;
var timerPaso = null;
/**
 * Dispara el dialogo con la pregunta para lanzarla
 * @param  {string} pregunta   
 * @param  {integer} idPregunta 
 * @param  {string} concursante
 * @param  {integer} idConcursante
 * @param  {integer} posicion
 * @param  {integer} idGenerada 
 */
function leer(pregunta,idPregunta,puntaje,concursante,idConcursante,posicion,idGenerada){
    $("#mdl-leer-pregunta").modal({backdrop: 'static', keyboard: false});
    $("#mdl-leer-pregunta").css("padding","1px");
    $("#p-pregunta").text(pregunta);
    $("#titulo_modal").text(" Valor de la pregunta: "+ puntaje);
    $("#ID_PREGUNTA").val(idPregunta);
    $("#ID_GENERADA").val(idGenerada);
    $("#ID_CONCURSANTE").val(idConcursante);
    $("#PREGUNTA_POSICION").val(posicion);
    $("#concursante").html("Equipo en turno: " + concursante);
}

/**
 * Lanza la pregunta para que se le aparezca a los concursantes
 * @param  {integer} segundos 
 */
function lanzarPregunta(segundos,boton){
	var generada = $("#ID_GENERADA").val();
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
    var categoria = $("#ID_CATEGORIA").val();
    var idConcursante =  $("#ID_CONCURSANTE").val();
    if(categoria == null || categoria == undefined){
        categoria = 'desempate';
    }
	$.ajax({
		type : 'POST',
        url  : 'class/PreguntasGeneradas.php',
        data :{'functionGeneradas':'lanzarPregunta2nda',
    			'ID_GENERADA':generada,
    			'ID_CONCURSO':concurso,
    			'ID_RONDA':ronda,
                'ID_CATEGORIA':categoria,
                'ID_CONCURSANTE': idConcursante},
        dataType: "json",
        success : function(response){
        	if(response.estado == 1){
                $(boton).hide(300);
                $("#loading").show(300)
                var respuestas = response.respuestas;
                var contenido = "<tr>";
                for(var r = 0; r < respuestas.length; r++){
                    contenido += "<td><h4>" + respuestas[r].INCISO + " ) ";
                    if(respuestas[r].ES_IMAGEN == 1){
                        contenido += "<img src='image/respuestas/" + respuestas[r].RESPUESTA + "'/>";
                    }else{
                        contenido += respuestas[r].RESPUESTA;
                    }
                    
                    contenido += "</h4></td>";
                }
                contenido += "</tr>";
                $("#content-respuestas tbody").html(contenido);
                cronometro(segundos,function(){contestoOpaso();}, function(){finCronometro();});
        	}else{
        		alert(response.mensaje);
        	}
        },
        error: function(error){
        	alert("ocurrio un error, por favor vuelve a intentar");
        }
	});	
}

function ocultarCronometro(){
    $("#reloj-cronometro").css("display","none");
}

function finCronometro(){
    ocultarCronometro();
    var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
    var pregunta = $("#ID_PREGUNTA").val();
    var idConcursante =  $("#ID_CONCURSANTE").val();
    $.ajax({
        type: "POST",
        url: "class/TableroPuntaje.php",
        data :{'functionTablero':'generaPuntajeTiempoFinalizado',
    			'ID_CONCURSO':concurso,
                'ID_RONDA':ronda,
                'ID_PREGUNTA':pregunta,
                'ID_CONCURSANTE': idConcursante},
        dataType: "json",
        success: function (response) {
            if(response.estado == 1){
                if(confirm(response.mensaje)){
                    tomoPaso(response.ID_CONCURSANTE);
                }else{
                    $("#btn-siguiente").show(300);
                }
            }else{
                alert(response.mensaje);
                finCronometro();
            }
        },error:function(error){
            console.log(error);
        }
    });
}

/**
 * Lanza la peticion para saber si todos los participantes contestaron
 */
function contestoOpaso(){
    $.ajax({
        url: 'class/TableroPuntaje.php',
        type: 'GET',
        dataType: 'json',
        data: {'ID_CONCURSO':$("#ID_CONCURSO").val(),
                'ID_RONDA':$("#ID_RONDA").val(),
                'PREGUNTA':$("#ID_PREGUNTA").val(),
                'ID_CONCURSANTE':$("#ID_CONCURSANTE").val(),
                'functionTablero':'contestoOpaso'},
        success:function(response){
            if(response.estado == 1){
                clearInterval(timerCronometro);
                ocultarCronometro();
                stopExecPerSecond = true;
                $("#btn-siguiente").show(300);
                $("#loading").hide(300);
            }else if(response.estado == 2){
                clearInterval(timerCronometro);
                ocultarCronometro();
                stopExecPerSecond = true;
                if(confirm(response.mensaje + response.concursante['CONCURSANTE'] + " ?")){
                    $("#loading").hide(300);
                    tomoPaso(response.concursante['ID_CONCURSANTE']);
                }else{
                    $("#loading").hide(300);
                    $("#btn-siguiente").show(300);
                }
            }
        },error:function(error){
            console.log(error);
        }
    }); 
}

function tomoPaso(concursante){
    $.ajax({
        url: 'class/TableroPuntaje.php',
        type: 'POST',
        dataType: 'json',
        data: {'ID_CONCURSO':$("#ID_CONCURSO").val(),
                'ID_RONDA':$("#ID_RONDA").val(),
                'PREGUNTA':$("#ID_PREGUNTA").val(),
                'ID_CONCURSANTE':$("#ID_CONCURSANTE").val(),
                'PREGUNTA_POSICION':$("#PREGUNTA_POSICION").val(),
                'functionTablero':'tomoPaso'},
        success:function(response){
            if(response.estado == 1){
                $("#btn-siguiente").hide(300);
                $("#loading").show(300);
                cronometroPaso($("#SEGUNDOS_PASO").val() 
                                ,function(){ contestoPaso(concursante); }
                                ,function(){ finCronometroPaso(); } );
            }else{
                alert(response.mensaje);
            }
        },
        error:function(error){
            alert("Ocurrio un error");
            console.log(error);
        }
    });
}

function ocultarCronometroPaso(){
    $("#reloj-paso").css("display","none");
}

function finCronometroPaso(){
    ocultarCronometroPaso();
    var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
    var pregunta = $("#ID_PREGUNTA").val();
    var idConcursante =  $("#ID_CONCURSANTE").val();
    $.ajax({
        type: "POST",
        url: "class/TableroPaso.php",
        data :{'functionTableroPaso':'generaPuntajeTiempoFinalizado',
    			'ID_CONCURSO':concurso,
                'ID_RONDA':ronda,
                'ID_PREGUNTA':pregunta,
                'ID_CONCURSANTE': idConcursante},
        dataType: "json",
        success: function (response) {
            if(response.estado == 1){
               $("#btn-siguiente").show(300); 
            }else{
                alert(response.mensaje);
                finCronometroPaso();
            }
        },error:function(error){
            console.log(error);
        }
    });
}

function contestoPaso(concursante){
    $.ajax({
        url: 'class/TableroPaso.php',
        type: 'GET',
        dataType: 'json',
        data: {'ID_CONCURSO':$("#ID_CONCURSO").val(),
                'ID_RONDA':$("#ID_RONDA").val(),
                'PREGUNTA':$("#ID_PREGUNTA").val(),
                'ID_CONCURSANTE':concursante,
                'functionTableroPaso':'pasoContestado'},
        success:function(response){
            if(response.estado == 1){
                clearInterval(timerCronometroPaso);
                ocultarCronometroPaso();
                stopExecPerSecond1 = true;
                $("#btn-siguiente").show(300);
                $("#loading").hide(300);
            }else{
                console.log('Aun no contesta');
            }
        },error:function(error){
            console.log(error);
        }
    }); 
}

/**
 * Resetea al estado inicial el modal si l ocierran
 */
function closeModal(){
    $("#mdl-leer-pregunta").modal('hide');
    $("#p-pregunta").text('');
    $("#ID_PREGUNTA").val('');
    $("#ID_GENERADA").val('');
    $("#tbl-generadas tbody").html('');
    $("#tbl-generadas tbody").html('')
    $("#tbl-generadas").hide();
    $("#btn-siguiente").hide();
    $("#btn-lanzar").show();
}


function siguienteRonda(){
    var concurso = $("#ID_CONCURSO").val();
    var categoria = $("#ID_CATEGORIA").val();
    var rondaActual = $("#ID_RONDA").val();
    $.ajax({
        url: 'class/RondasLog.php',
        type: 'POST',
        dataType: 'json',
        data: {'ID_CONCURSO': concurso , 
                'ID_CATEGORIA': categoria , 
                'functionRondasLog':'siguienteRonda',
                'rondaActual':rondaActual,
                'IS_DESEMPATE':document.getElementById('IS_DESEMPATE').value,
                'NIVEL_EMPATE':document.getElementById('NIVEL_EMPATE').value},
        success:function(response){
            if(response.estado == 1 ){
                location.reload();
            }else if(response.estado == 2){
                alert(response.mensaje);
                window.location.replace('panel');
            }else if(response.estado == 3){
                alert("A continuacion la 2da ronda grupal");
                window.location.replace("lanzador_2dn_grupal");
            }else{
                alert(response.mensaje);
            }
        },
        error:function(error){
            alert("ERROR");
            console.log(error);
        }
    })
}

function mostrarResumen(){
    $("#divtablero").slideToggle(500);
}

$(document).ready(function(){
    $('#mdl-leer-pregunta').on('hidden.bs.modal', function () {
        closeModal();
    });
});