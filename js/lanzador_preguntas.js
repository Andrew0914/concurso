/**
 * Dispara el dialogo con la pregunta para lanzarla
 * @param  {[int]} pregunta   
 * @param  {[int]} idPregunta 
 * @param  {[int]} idGenerada 
 */
function leer(pregunta,idPregunta,puntaje, idGenerada){
    $("#mdl-leer-pregunta").modal({backdrop: 'static', keyboard: false});
    $("#mdl-leer-pregunta").css("padding","1px");
    $("#p-pregunta").text(pregunta);
    $("#titulo_modal").text(" Valor de la pregunta: "+ puntaje);
    $("#ID_PREGUNTA").val(idPregunta);
    $("#ID_GENERADA").val(idGenerada);
}

/**
 * Lanza la pregunta para que se le aparezca a los concursantes
 * @param  integer segundos 
 */
function lanzarPregunta(segundos,boton){
	var generada = $("#ID_GENERADA").val();
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
    var categoria = $("#ID_CATEGORIA").val();
    if(categoria == null || categoria == undefined){
        categoria = 'desempate';
    }
	$.ajax({
		type : 'POST',
        url  : 'class/PreguntasGeneradas.php',
        data :{'functionGeneradas':'lanzarPregunta',
    			'ID_GENERADA':generada,
    			'ID_CONCURSO':concurso,
    			'ID_RONDA':ronda,
                'ID_CATEGORIA':categoria,
                'NIVEL_EMPATE': document.getElementById('NIVEL_EMPATE').value,
                'IS_DESEMPATE': document.getElementById('IS_DESEMPATE').value},
        dataType: "json",
        success : function(response){
        	if(response.estado == 1){
                $(boton).hide(300);
                // activamos el cronometro
				cronometro(segundos,function(){
                    todosContestaron();
                },function(){
                    stopExecPerSecond= true;
                    afterAnswer();
                });
                var respuestas = response.respuestas;
                var contenido = "<tr>";
                for(var r = 0; r < respuestas.length; r++){
                    contenido += "<td><h4>" + respuestas[r].INCISO + " ) "+ respuestas[r].RESPUESTA + "</h4></td>";
                }
                contenido += "</tr>";
                $("#content-respuestas tbody").html(contenido);
        	}else{
        		alert(response.mensaje);
        	}
        },
        error: function(error){
        	alert("ocurrio un error, por favor vuelve a intentar");
        }
	});	
}

/**
 * Lanza la peticion para saber si todos los participantes contestaron
 */
function todosContestaron(){
   var concurso = $("#ID_CONCURSO").val();
   var ronda = $("#ID_RONDA").val();
   var pregunta = $("#ID_PREGUNTA").val();
   var nivelEmpate = $("#NIVEL_EMPATE").val();
   $.ajax({
       url: 'class/TableroPuntaje.php',
       type: 'GET',
       dataType: 'json',
       data: {'functionTablero':'todosContestaron','ID_CONCURSO':concurso,'ID_RONDA':ronda,'ID_PREGUNTA': pregunta, 'NIVEL_EMPATE':nivelEmpate},
       success:function(response){
        if(response.estado == 1){
            stopExecPerSecond= true;
            notFinish = true;
            afterAnswer();
        }
        getActividadPregunta();
       },
       error:function(error){
        console.log(error);
       }
   });
}

function afterAnswer(){
    setTimeout(function(){
        getMarcadorPregunta();
    },1000);
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

/**
 * Obtiene los numeros para el histograma de la pregunta correctas/incorrectas al final de la pregunta
 * @return {[type]} [description]
 */
function getMarcadorPregunta(){
    var concurso = $("#ID_CONCURSO").val();
    var ronda = $("#ID_RONDA").val();
    var pregunta = $("#ID_PREGUNTA").val();
    $.ajax({
        url: 'class/TableroPuntaje.php',
        type: 'GET',
        dataType: 'json',
        data: {'functionTablero':'getMarcadorPregunta','ID_CONCURSO':concurso,'ID_RONDA':ronda,'ID_PREGUNTA': pregunta},
        success:function(response){
            if(response.estado == 1){
                $("#num_incorrectas").text(response.incorrectas);
                $("#histo-incorrectas").css({
                    'width': response.por_incorrectas + "%",
                    'background-color': 'red'
                });
                $("#num_correctas").text(response.correctas);
                $("#histo-correctas").css({
                    'width': response.por_correctas + "%",
                    'background-color': 'green'
                });
                $("#reloj-cronometro").css("display","none");
                $("#btn-siguiente").show(300);
                $("#animated text").text("00:00");
            }
        },
        error:function(error){
            console.log(error);
        }
    });
}

/**
 * Obtiene los marcadores para la pregunta contestaron y no contestaron
 */
function getActividadPregunta(){
    var concurso = $("#ID_CONCURSO").val();
    var ronda = $("#ID_RONDA").val();
    var pregunta = $("#ID_PREGUNTA").val();
    $.ajax({
        url: 'class/TableroPuntaje.php',
        type: 'GET',
        dataType: 'json',
        data: {'functionTablero':'getActividadPregunta','ID_CONCURSO':concurso,'ID_RONDA':ronda,'ID_PREGUNTA': pregunta},
        success:function(response){
            if(response.estado == 1){
                $("#num_contestadas").text(response.contestadas);
                $("#histo-contestadas").css({
                    'width': response.porcentaje_contestadas + "%",
                    'background-color': 'gray'
                });
                $("#num_nocontestadas").text(response.no_contestadas);
                $("#histo-nocontestadas").css({
                    'width': response.por_no_contestadas + "%",
                    'background-color': 'black'
                });
            }
        },
        error:function(error){
            console.log(error);
        }
    });
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

$(document).ready(function(){
    $('#mdl-leer-pregunta').on('hidden.bs.modal', function () {
        closeModal();
    });
});