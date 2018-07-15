/**
 * Dispara la peticion que genera lasp reguntas y setea los contadores en la tabla
 * @param  {[int]} concurso 
 * @param  {[int]} ronda    
 */
function generaPreguntas(concurso,etapa){
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

/**
 * Funciaon para verificar si un campo es null y no pintar 'null'
 * @param  object valor 
 * @return object
 */
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

/**
 * Inicia las preguntas para la categoria elegida
 * @param  integer categoria 
 * @param  integer concurso  
 * @param  integer etapa     
 */
function iniciarCategoria(categoria,concurso){
	$.ajax({
		url: 'class/Concurso.php',
		type: 'POST',
		dataType: 'json',
		data: {'ID_CONCURSO':concurso, 'ID_CATEGORIA' : categoria, 'functionConcurso':'iniciarCategoriaRonda' },
		success: function(response){
			if(response.estado == 1){
				window.location.replace('leer_preguntas');
			}else{
				alert(response.mensaje);
			}
		},
		error: function(xhr , text , error){
			console.log(error);
			alert("Ocurrio error inesperado: " + text);
		}
	});
}

function generaTableros(concurso){
	$.ajax({
		url: 'class/TableroPosiciones.php',
		type: 'POST',
		dataType: 'json',
		data: {'functionTabPosiciones': 'generaPosiciones',
				'ID_CONCURSO' : concurso},
		success:function(response){
			alert(response.mensaje);
			if(response.estado == 1){
				window.location.replace("tablero?id_master="+response.tablero_master);
			}
		},
		error:function(error){
			alert("No se pudieron generar los tableros de puntajes, porfavor intentalo de nuevo");
			console.log(error);
		}
	});	
}