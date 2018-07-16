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
                'ID_CATEGORIA':categoria},
        dataType: "json",
        success : function(response){
        	if(response.estado == 1){
                $(boton).hide(300);
                // activamos el cronometro
				cronometro(segundos,function(){
                    todosContestaron();
                    getActividadPregunta();
                },function(){
                    stopExecPerSecond= true;
                    getMarcadorPregunta();
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
   $.get('class/TableroPuntaje.php?functionTablero=todosContestaron&ID_CONCURSO='+concurso+'&ID_RONDA='+ronda+'&ID_PREGUNTA='+pregunta, 
      function(data) {
        if(data.estado == 1){
            stopExecPerSecond= true;
            //obtenemos como les fue a los participantes
            getMarcadorPregunta();
         }
   },'json');
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
        url: 'class/TableroPuntaje.php?functionTablero=getMarcadorPregunta',
        type: 'GET',
        dataType: 'json',
        data: 'ID_CONCURSO='+concurso+'&ID_RONDA='+ronda+'&ID_PREGUNTA='+pregunta,
        success:function(response){
            if(response.estado == 1){
                notFinish = true;
                stopExecPerSecond= true;
                $("#reloj-cronometro").css("display","none");
                $("#animated text").text(0);
                var marcadores = response.marcadores;
                var correctas = 0;
                var incorrectas = 0;
                for (var m= 0; m< marcadores.length ; m++ ){
                    correctas = marcadores[m].correctas;
                    incorrectas = marcadores[m].incorrectas;
                }
                var concursantes = response.cont_concursantes[0].total;
                $("#btn-siguiente").show(300);
                var porcentajeCorrectas = ((correctas * 100) / concursantes) + '%';
                $("#num_correctas").text(correctas);
                $("#histo-correctas").css({
                    'width': porcentajeCorrectas,
                    'background-color': 'green'
                });
                var porcentajeIncorrectas = ((incorrectas * 100) / concursantes) + '%';
                $("#num_incorrectas").text(incorrectas);
                $("#histo-incorrectas").css({
                    'width': porcentajeIncorrectas,
                    'background-color': 'red'
                });
                getActividadPregunta();
            }else{
                alert(response.mensaje);
            }
        },
        error:function(error){}
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
        url: 'class/TableroPuntaje.php?functionTablero=getActividadPregunta',
        type: 'GET',
        dataType: 'json',
        data: 'ID_CONCURSO='+concurso+'&ID_RONDA='+ronda+'&ID_PREGUNTA='+pregunta,
        success:function(response){
            if(response.estado == 1){
                var marcadores = response.marcadores;
                var contestadas = 0;
                var nocontestadas = 0;
                for (var m= 0; m< marcadores.length ; m++ ){
                    contestadas = marcadores[m].contestadas;
                    nocontestadas = marcadores[m].nocontestadas;
                }
                var concursantes = response.cont_concursantes[0].total;
                var porcentajaContestadas = ((contestadas * 100) / concursantes) + '%';
                $("#num_contestadas").text(contestadas);
                $("#histo-contestadas").css({
                    'width': porcentajaContestadas,
                    'background-color': '#BDBDBD'
                });
                var porcentajeNocontestadas = ((nocontestadas * 100) / concursantes) + '%';
                if(nocontestadas == null){ 
                    porcentajeNocontestadas = '100%';
                    nocontestadas = concursantes;
                }
                $("#num_nocontestadas").text(nocontestadas);
                $("#histo-nocontestadas").css({
                    'width': porcentajeNocontestadas,
                    'background-color': '#848484'
                });

            }else{
                alert(response.mensaje);
            }
        },
        error:function(error){}
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