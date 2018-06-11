function generaPreguntas(concurso,ronda){
	var categoria = $("#ID_CATEGORIA").val();
	if(categoria != ""){
		$.ajax({
			url: 'class/PreguntasGeneradas.php',
			type: 'POST',
			dataType: 'json',
			data: {'ID_CONCURSO': concurso,
					'ID_RONDA':ronda,
					'ID_CATEGORIA':categoria,
					'functionGeneradas':'generaPreguntas'},
			success: function(response){
				alert(response.mensaje);
			},
			error:function(error){
				alert("Ocurrio un error inesperado");
				console.log(error);
			}
		});
	}else{
		alert("Debes seleccionar una categoria");
	}
}