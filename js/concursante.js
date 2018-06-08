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
          	window.location.replace("individual_ronda1");
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