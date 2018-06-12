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

function lanzarPregunta(segundos){
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
				cronometro(segundos,function(){
					location.reload();	
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