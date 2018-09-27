/**
 * Permite acceder a la pantalla de inicio de concurso
 * @param  {form} formulario 
 */
function accederConcurso(formulario){
	var ajaxTask = $.ajax({
		type : 'POST',
		url  : 'class/Concursante.php',
		data :$(formulario).serialize()+"&functionConcursante=accederConcurso",
		dataType: "json",
		success : function(response){
			if(response.estado == 1){
				accederRonda(response.concursante.ID_RONDA);
			}else{
				alert(response.mensaje);
			}
		},
		error : function(error){
			alert("Oops! ocurrio un error inesperado, actualiza la pagina e intenta de nuevo");
			console.log(error);
		},complete: function(){
			ajaxTask = null;
		}
	});
}

/**
 * Obtiene la lista de concursantes por concurso
 * @param {int} concurso 
 */
function setConcursantes(concurso){
  var idConcurso = $(concurso).val();
  var ajaxTask = $.ajax({
		type: "GET",
		url: "class/Concursante.php",
		data: {
			"concurso":idConcurso,
			"functionConcursante": "getConcursantes"
		},
		dataType: "json",
		success: function (data) {
			var concursantes = data.concursantes;
			var content = "<option value=''>Elige un concursante</option>";
			for(var d=0; d<concursantes.length; d++){
			content += "<option value='" + concursantes[d].CONCURSANTE;
			content += "'>" + concursantes[d].CONCURSANTE + "</option>";
			}
			$("#CONCURSANTE").css("width","75%");
			$("#CONCURSANTE").html(content);
			content = null;
			concursantes = null;
		},error:function(error){
			console.log(error);
		},complete: function(){
			ajaxTask = null;
			idConcurso = null;
		}
  });
}

function accederDesempate(idConcurso,concursante){
	var ajaxTask = $.ajax({
		url: 'class/Concursante.php',
		type: 'GET',
		dataType: 'json',
		data: {'functionConcursante':'accederDesempate' , 'ID_CONCURSO':idConcurso,'ID_CONCURSANTE':concursante},
		success:function(response){
			if(response.estado == 1){
				if(response.empatado == 1){
					accederRonda(response.ronda);
				}else{
					window.location.replace('concurso_finalizado');
				}
			}else{
				alert(response.mensaje);
			}
		},error:function(error){
			alert("Vuelve a intentar :( ");
			console.log(error);
		},complete:function(){
			ajaxTask = null;
		}
	});
}

$(document).ready(function(){
	// atrapamos el evento subit para acceder al concurso
	$("#form-accede-concurso").on('submit', function(event) {
		event.preventDefault();
		accederConcurso(document.getElementById("form-accede-concurso"));
	});
});