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
            $("#mensaje_concurso").css('display', 'none');
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

/**
 * Genera el html de las preguntas y respuestas asi como los inputs para responder y los pone de a uno en el DOC
 * @param  {array} preguntas 
 * @param  {object} ronda     
 */
function generaContenido(preguntas,ronda){
    var contenido = "";
    var respuestas = null;
    for (var i = 0; i< preguntas.length ; i++) {
        contenido += "<div class='preguntadisplay'><p class='initPreguntas pposicion"+i+"'>" + preguntas[i].PREGUNTA + "</p";
        respuestas = preguntas[i].respuestas;
        contenido += "<div class='row initPreguntas pposicion"+i+"'>";
        for(var x= 0; x < respuestas.length ; x++){
            contenido += "<div class='col-md-3'>";
            contenido += "<input type='radio' name='PyR"+i+"' value='"+respuestas[x].ID_PREGUNTA + "-"+ respuestas[x].ID_RESPUESTA+"'/>";
            contenido += respuestas[x].INCISO + ") " + respuestas[x].RESPUESTA
            contenido += "</div>";
        }
        contenido += "</div></div>";
        $("form#form-individual1 .preguntadisplay:last").after(contenido);
    }

    iniciaRonda(preguntas.length , ronda);
}

/**
 * Inicia la ronda y la consecucion de lasp reguntas
 * @param  {int} cantidad 
 * @param  {object} ronda    
 */
function iniciaRonda(cantidad , ronda){

    $("body").removeClass('azul');
    $("body").addClass('blanco');
    $("#form-individual1").addClass("card-lg");

    var msPorPregunta = ronda.SEGUNDOS_POR_PREGUNTA * 1000;
    var showIndex = 0;
    var hideIndex = 0;
    var timerPregunta = setInterval(function(){
        if(showIndex > 0){
            hideIndex = showIndex-1;
            $(".pposicion"+hideIndex).css("display","none");
        }
        $(".pposicion"+showIndex).css("display","block");
        // iniciamos el cronometro visual
        cronometro(ronda.SEGUNDOS_POR_PREGUNTA);
        showIndex++;
        if(showIndex == (cantidad-1)){
            clearInterval(timerPregunta);
        }
    },msPorPregunta);
}

$(document).ready(function(){
	//listenerInicio();
});