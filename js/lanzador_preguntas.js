/**
 * Dispara el dialogo con la pregunta para lanzarla
 * @param  {[int]} pregunta   
 * @param  {[int]} idPregunta 
 * @param  {[int]} idGenerada 
 */
function leer(pregunta,idPregunta, idGenerada){
    $("#mdl-leer-pregunta").modal();
    $("#p-pregunta").text(pregunta);
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
	$.ajax({
		type : 'POST',
        url  : 'class/PreguntasGeneradas.php',
        data :{'functionGeneradas':'lanzarPregunta',
    			'ID_GENERADA':generada,
    			'ID_CONCURSO':concurso,
    			'ID_RONDA':ronda},
        dataType: "json",
        success : function(response){
        	if(response.estado == 1){
                $(boton).hide(300);
                // activamos el cronometro
				cronometro(segundos,function(){
                    todosContestaron();
                },function(){
                    stopExecPerSecond= true;
                    getMarcadorPregunta();
                });

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
 * Obtiene los marcadores para la pregunta
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
                $("#cronometro-content").css("display","none");
                $("#animated text").text(0);
                var marcadores = response.marcadores;
                var contenido = "";
                for (var m= 0; m< marcadores.length ; m++ ){
                    contenido += "<tr>";
                    contenido += "<td>" + marcadores[m].CONCURSANTE + "</td>";
                    contenido += "<td> <img src='image/" + marcadores[m].RESULTADO + ".png'/></td>";
                    contenido += "</tr>";
                }
                $("#tbl-marcador-pregunta tbody").html(contenido);
                $("#tbl-marcador-pregunta").show(300);
                $("#btn-siguiente").show(300);
            }else{
                alert(response.mensaje);
            }
        },
        error:function(error){}
    });
}

$(document).ready(function(){
    $('#mdl-leer-pregunta').on('hidden.bs.modal', function () {
        closeModal();
    });
});