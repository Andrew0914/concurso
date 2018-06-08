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
        contenido += "<input type='hidden' id='mPregunta"+i+"' value='"+preguntas[i].ID_PREGUNTA+"'/>"
        for(var x= 0; x < respuestas.length ; x++){
            contenido += "<div class='col-md-3 centrado text-answer'>";
            contenido += "<button type='button' class='btn-answer' onclick='eligeInciso(this)'>"+incisos[x]+"</button><br><br>";
            contenido += "<input type='radio' name='mRespuesta"+i+"' value='"+ respuestas[x].ID_RESPUESTA +"' style='display:none'/>";
            if(respuestas[x].ES_IMAGEN == 1){
                contenido += "<img src='image/respuestas/" + respuestas[x].RESPUESTA + "'/>";
            }else{
                contenido += respuestas[x].RESPUESTA;
            }
            
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
            // cuando se acabo el tiempo de la anterior mandamos la respuesta
            sendRespuesta(hideIndex);
        }
        $(".pposicion"+showIndex).css("display","flex");
        // iniciamos el cronometro visual
        cronometro(ronda.SEGUNDOS_POR_PREGUNTA);
        showIndex++;
        if(showIndex == cantidad){
            // aqui se manda la ultima pregunta que ya no regresa al timer
            clearInterval(timerPregunta);
            setTimeout(function(){
                sendRespuesta((showIndex - 1));
                finalizaRonda();
            },msPorPregunta);  
        }
    },msPorPregunta);
}

function finalizaRonda(){
    alert("Felicidades, has concluido la primer ronda de la etapa individual");
    window.location.replace("individual_ronda2");
}

/**
 * Selecciona la respuesta que sera enviada
 * @param  {button} boton [opcion oprimida]
 */
function eligeInciso(boton){
    // reseteamos los estilos de los no checados
    $(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
    $(boton).addClass('btn-checked');
    // checamos el radio oculto
    $($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
}

/**
 * Manda la respuesta a la base de datos
 * @param  {int} posicionPreguntaTerminada
 */
function sendRespuesta(posicionPreguntaTerminada){
    // datos generales para el tablero
    var concurso = $("#ID_CONCURSO").val();
    var ronda = $("#ID_RONDA").val();
    var concursante = $("#ID_CONCURSANTE").val();
    // datos por pregunta y respuesta para el tablero
    var pregunta = $("#mPregunta"+posicionPreguntaTerminada).val();
    var respuesta = 0;
    $("input[name=mRespuesta"+posicionPreguntaTerminada+"]").each(
        function(index, el) {
            if(el.checked){
                respuesta = el.value;
            }
    });
    var posicion = posicionPreguntaTerminada + 1;
    // estructura para enviarla via post
    var requestData= {"ID_CONCURSO":concurso ,
                       "ID_RONDA":ronda,
                       "ID_CONCURSANTE":concursante,
                       "PREGUNTA_POSICION":posicion,
                       "PREGUNTA":pregunta,
                       "RESPUESTA":respuesta,
                       "functionTablero":"guardar" };
    //almacenamos
    $.post('class/TableroPuntaje.php', 
        requestData, 
        function(data, textStatus, xhr) {
            /*console.log("data: " + data);
            console.log("textStatus: " + textStatus);
            console.log("xhr: " + xhr );*/
    },'json');
}

$(document).ready(function(){
	listenerInicio();
});