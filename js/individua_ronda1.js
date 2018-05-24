/**
 * Lanza la peticion al listener de inicio de concurso primera ronda y cacha la respuesa una vez que se activa
 */
function listenerInicio(){
	$.ajax({
        type : 'GET',
        url  : 'class/listeners/listener_inicio_concurso.php',
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
            var preguntas = response.preguntas;
            var ronda = response.ronda;
            generaContenido(preguntas,ronda);
            $("#mensaje_concurso").html(response.mensaje+"<br><br>");
          }else{
          	alert(response.mensaje);
          }
        },
        error : function(jqXHR , textStatus , errorThrown){
        	alert("Oops! ocurrio un error inesperado: " + jqXHR.status + " message: " + textStatus);
          	console.log("js.individua_ronda1.listenerInicio.ajax: " + jqXHR.status 
                + " message: " + textStatus 
                + " error:" + errorThrown);
        }
     });
}


function generaContenido(preguntas,ronda){
    var contenido = "";
    var respuestas = null;
    for (var i = 0; i< preguntas.length ; i++) {
        contenido = "<tr class='initPreguntas pposicion"+i+"'><td colspan='2'><b>Categoria:</b> " + preguntas[i].CATEGORIA + "</td>";
        contenido += "<td colspan='2'><b>Dificultad:</b> " + preguntas[i].DIFICULTAD + "</td></tr>";
        contenido += "<tr class='initPreguntas pposicion"+i+"'><td colspan='4'>" + preguntas[i].PREGUNTA + "</td></tr>";
        respuestas = preguntas[i].respuestas;
        contenido += "<tr class='initPreguntas pposicion"+i+"'>";
        for(var x= 0; x < respuestas.length ; x++){
            contenido += "<td colspan='1'>";
            contenido += "<input type='radio' name='PyR"+i+"' value='"+respuestas[x].ID_PREGUNTA + "-"+ respuestas[x].ID_RESPUESTA+"'/>";
            contenido += respuestas[x].INCISO + ") " + respuestas[x].RESPUESTA
            contenido += "</td>";
        }
        contenido += "</tr>";
        $("form#form-individual1 table tr:last").after(contenido);
    }

    iniciaRonda(preguntas.length , ronda);
}

function iniciaRonda(cantidad , ronda){
    var msPorPregunta = ronda.SEGUNDOS_POR_PREGUNTA * 1000;

    var showIndex = 0;
    var hideIndex = 0;
    var timerPregunta = setInterval(function(){
        if(showIndex > 0){
            hideIndex = showIndex-1;
            $(".pposicion"+hideIndex).css("display","none");
        }
        $(".pposicion"+showIndex).css("display","block");
        var sPorPregunta = ronda.SEGUNDOS_POR_PREGUNTA;
        var cronometro = setInterval(function(){
            $("#cronometro").html("<h3>Tiempo: " +(sPorPregunta--) +" segundos</h3>");
            if(sPorPregunta == 0){
                clearInterval(cronometro);
            }
        },1000);
        showIndex++;
        if(showIndex == (cantidad-1)){
            clearInterval(timerPregunta);
        }
    },msPorPregunta);
}

$(document).ready(function(){
	listenerInicio();
});