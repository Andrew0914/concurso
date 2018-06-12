/**
 * Dispara la peticion que genera lasp reguntas y setea los contadores en la tabla
 * @param  {[int]} concurso 
 * @param  {[int]} ronda    
 */
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
				if(response.estado==1){
					var counts = response.counts;
					var contenido="";
					var total = 0;
					for(var x=0; x < counts.length ; x++){
						contenido +="<tr><td>Geofisica</td><td>"+counts[x].geofisica+"</td></tr>";
						contenido +="<tr><td>Geologia</td><td>"+counts[x].geologia+"</td></tr>";
						contenido +="<tr><td>Petroleros</td><td>"+counts[x].petroleros+"</td></tr>";
						contenido +="<tr><td>Generales</td><td>"+counts[x].generales+"</td></tr>";
						total = (parseInt(counts[x].geofisica) + parseInt(counts[x].geologia) 
						+ parseInt(counts[x].petroleros) + parseInt(counts[x].generales));
						contenido +="<tr><td><b>TOTAL</b></td><td>"+total+"</td></tr>";
					}
					$("#tbl-generadas tbody").html(contenido);
				}
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

/**
 * Cambiar y finaliza la ronda actual
 * @param  {[int]} concurso    
 * @param  {[int]} rondaActual 
 */
function cambiarFinalizarRonda(concurso,rondaActual){
	var rondaNueva = $("#RONDA_NUEVA").val();
	if(rondaNueva !=""){
		$.post('class/RondasLog.php',
			{'functionRondasLog': 'cambiarFinalizar',
			 'ID_CONCURSO':concurso,
			 'RONDA_ACTUAL': rondaActual,
			 'RONDA_NUEVA':rondaNueva}, 
			function(data, textStatus, xhr) {
				alert(data.mensaje);
				if(data.estado == 1){
					location.reload();
				}
		},'json');
	}else{
		alert("Elige una ronda");
	}
}