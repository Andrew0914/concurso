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
    var incisos =['a','b','c','d'];
    for (var i = 0; i< preguntas.length ; i++) {
        respuestas = preguntas[i].respuestas;
        contenido += "<div class='row initPreguntas pposicion"+i+"'><div class='col-md-12 text-pregunta'>"+preguntas[i].PREGUNTA + "</div></div>";
        contenido += "<div class='row initPreguntas pposicion"+i+"'>";
        for(var x= 0; x < respuestas.length ; x++){
            contenido += "<div class='col-md-3 centrado text-answer'>";
            contenido += "<button type='button' class='btn-answer' onclick='eligeInciso(this)'>"+incisos[x]+"</button><br><br>";
            contenido += "<input type='radio' name='PyR"+i+"' value='"+respuestas[x].ID_PREGUNTA + "-"+ respuestas[x].ID_RESPUESTA+"' style='display:none'/>";
            contenido += respuestas[x].RESPUESTA;
            contenido += "</div>";
        }
        contenido += "</div>";
        $("form#form-individual1").html(contenido);
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
    $("#card-inicio").hide(500);
    $("#cronometro-content").show(500);
    var msPorPregunta = ronda.SEGUNDOS_POR_PREGUNTA * 1000;
    var showIndex = 1;
    var hideIndex = 0;
    // INICIO MOSTRAR primer pregunta
    $(".pposicion0").css("display","flex");
    cronometro(ronda.SEGUNDOS_POR_PREGUNTA);
    //FIN
    //INICIO SECUENCIA 
    var timerPregunta = setInterval(function(){
        if(showIndex > 0){
            hideIndex = showIndex-1;
            $(".pposicion"+hideIndex).css("display","none");
        }
        $(".pposicion"+showIndex).css("display","flex");
        // iniciamos el cronometro visual
        cronometro(ronda.SEGUNDOS_POR_PREGUNTA);
        showIndex++;
        if(showIndex == cantidad){
            clearInterval(timerPregunta);
        }
    },msPorPregunta);
}

function eligeInciso(boton){
    // reseteamos los estilos de los no checados
    $(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
    $(boton).addClass('btn-checked');
    // checamos el radio oculto
    $($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
}

$(document).ready(function(){
	listenerInicio();
});