/**
 * Dispara la peticion que genera lasp reguntas y setea los contadores en la tabla
 * @param  {[int]} concurso 
 * @param  {[int]} ronda    
 */
function generaPreguntas(concurso,ronda,etapa){
	var categoria = $("#ID_CATEGORIA").val();
	if(categoria != ""){
		$.ajax({
			url: 'class/PreguntasGeneradas.php',
			type: 'POST',
			dataType: 'json',
			data: {'ID_CONCURSO': concurso,
					'ID_CATEGORIA':categoria,
					'ID_ETAPA':etapa,
					'functionGeneradas':'generaPreguntas'},
			success: function(response){
				if(response.estado==1){
					var contadores = response.counts.contadores;
					var contenido="";
					for(var x=0; x < contadores.length ; x++){
						contenido += "<tr>";
						contenido += "<td>" + esNull(contadores[x]['ronda']) + "</td>";
						contenido += "<td>" + esNull(contadores[x]['geofisica']) + "</td>";
						contenido += "<td>" + esNull(contadores[x]['geologia']) + "</td>";
						contenido += "<td>" + esNull(contadores[x]['petroleros']) + "</td>";
						contenido += "<td>" + esNull(contadores[x]['generales'] ) + "</td>";
						contenido += "</tr>";
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

function esNull(valor){
	if(valor === "null" || valor === undefined || valor === null){
		return '';
	}
	return valor;
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
					window.location.replace('panel');
				}
		},'json');
	}else{
		alert("Elige una ronda");
	}
}