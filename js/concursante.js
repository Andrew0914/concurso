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