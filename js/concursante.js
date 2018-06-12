/**
 * Permite acceder a la pantalla de inicio de concurso
 * @param  {form} formulario 
 */
function accederConcurso(formulario){
	$.ajax({
        type : 'POST',
        url  : 'class/Concursante.php',
        data :$(formulario).serialize()+"&functionConcursante=accederConcurso",
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
            switch(response.concursante.ID_RONDA){
              case 1:
                window.location.replace("individual_ronda1");
              break;
              case 2:
                window.location.replace("individual_ronda2");
              break;
              case 3:
                window.location.replace("individual_desempate");
              break;
              case 4:
                window.location.replace("grupal_ronda1");
              break;
              case 5:
                window.location.replace("grupal_ronda2");
              break;
              case 6:
                window.location.replace("grupal_desempate");
              break;
              default:
                alert("No pudimos redirigirte a tu concurso, intenta de nuevo");
            }
          	
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

/**
 * Obtiene la lista de concursantes por concurso
 * @param {int} concurso 
 */
function setConcursantes(concurso){
  var idConcurso = $(concurso).val();
  $.get('class/Concursante.php?concurso='+idConcurso+"&functionConcursante=getConcursantes",
    function(data) {
      var concursantes = data.concursantes;
      var content = "";
      for(var d=0; d<concursantes.length; d++){
        content += "<option value='" + concursantes[d].CONCURSANTE;
        content += "'>" + concursantes[d].CONCURSANTE + "</option>";
      }
      $("#CONCURSANTE").html(content);
  },'json');
}