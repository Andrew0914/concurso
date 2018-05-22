function listenerInicio(){
	$.ajax({
        type : 'GET',
        url  : 'class/listeners/listener_inicio_concurso.php',
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
            $("#mensaje_concurso").html(response.mensaje+"<br><br>");
            var preguntas = response.preguntas;
            var contenido = "";
            var respuestas = null;
            for (var i = 0; i< preguntas.length ; i++) {
            	contenido += "CATEGORIA: " + preguntas[i].CATEGORIA + "<br>";
            	contenido += "DIFICULTAD: " + preguntas[i].DIFICULTAD + "<br>";
            	contenido += "PREGUNTA: " + preguntas[i].PREGUNTA + "<br>";
            	respuestas = preguntas[i].respuestas;
            	contenido += "<div class='row'>";
            	for (var r= 0; r< respuestas.length ; r++) {
            		contenido += "<div class='col-md-3'><input type='radio' value='"+respuestas[r].ID_RESPUESTA + "'/>";
            		contenido += "&nbsp;"+respuestas[r].RESPUESTA + "</div>";
            	}
            	contenido += "</div><br><hr>";
            }
  
            $("#preguntas").html(contenido)
          }else{
          	alert(response.mensaje);
          }
        },
        error : function(error){
        	alert("Oops! ocurrio un error inesperado, actualiza la pagina e intenta de nuevo");
          	console.log(error);
        }
     });
}

$(document).ready(function(){
	listenerInicio();
});