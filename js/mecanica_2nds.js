var lanzada = 0;
function obtenerPregunta(boton){
	$.ajax({
		url: 'class/PreguntasGeneradas.php',
		type: 'GET',
		dataType: 'json',
		data: {'ID_CONCURSO':$("#ID_CONCURSO").val(),
				'ID_RONDA':$("#ID_RONDA").val(),
				'ID_CATEGORIA':$("#ID_CATEGORIA").val() ,
				'ID_CONCURSANTE':$("#ID_CONCURSANTE").val(),
				'functionGeneradas':'miUltimaLanzada'},
		success:function(response){
			if(response.estado == 1){
				if(response.pregunta[0].LANZADA > lanzada){
					showPregunta(response);
					lanzada = response.pregunta[0].LANZADA;
				}else{
					alert("Espera a que te lancen una pregunta");
				}
			}else {
				alert(response.mensaje);
			}
		},error:function(error){
			console.log(error);
		}
	});
}

function showPregunta(response){
	// segundo para cada pregunta
	var segundos = $("#segundos_ronda").val();
	var segundosReales = segundos - response.pregunta[0].TIEMPO_TRANSCURRIDO;
	if(segundosReales <= 0){
		alert("Vaya! parece que el tiempo para obtener tu pregunta a terminado :(");
	}else{
		//cambiamos la vista
		$("body").removeClass('azul');
		$("body").addClass('blanco');
		$("#card-inicio").hide(300);
		$("#btn-obtener-pr").hide(300);
		$("#btn-obtener-pr-paso").hide(300);
		$("#btn-paso").show(300);
		$("#pregunta").show(300);
		$("#resultado-mi-pregunta").html("");
		// seteamos los valores de lap regunta a mostrar
		$("#pregunta p").text(response.pregunta[0].PREGUNTA);
		$("#ID_PREGUNTA").val(response.pregunta[0].ID_PREGUNTA);
		$("#PREGUNTA_POSICION").val(response.pregunta[0].PREGUNTA_POSICION);
		// mostramos las respuestas posibles para la pregunta
		var respuestas = response.pregunta.respuestas;
		var incisos =['a','b','c','d'];
		var contenido = "<div class='row'>";
		for(var x= 0; x < respuestas.length ; x++){
			contenido += "<div class='col-md-3 centrado text-answer'>";
			contenido += "<button type='button' class='btn-answer' onclick='eligeInciso(this)'>"+incisos[x]+"</button><br><br>";
			contenido += "<input type='radio' name='mRespuesta-"+response.pregunta[0].ID_PREGUNTA+"' value='"+ respuestas[x].ID_RESPUESTA +"' style='display:none'/>";
			if(respuestas[x].ES_IMAGEN == 1){
				contenido += "<img src='image/respuestas/" + respuestas[x].RESPUESTA + "'/>";
			}else{
				contenido += respuestas[x].RESPUESTA;
			}
			contenido += "</div>";
		}
		contenido += "</div>";
		$("#content-respuestas").html(contenido);
		cronometro(segundosReales,function(){},function(){paso();});
	}
	
}

function eligeInciso(boton){
	// reseteamos los estilos de los no checados
	 $(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
	 $(boton).addClass('btn-checked');
	 $($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
	 saveRespuesta(0);
}

function saveRespuesta(paso){
	var posicion = $("#PREGUNTA_POSICION").val();
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var pregunta = $("#ID_PREGUNTA").val();
	var concursante = $("#ID_CONCURSANTE").val();
	var respuestas = document.getElementsByName("mRespuesta-" + pregunta );
	var nivelEmpate = $("#NIVEL_EMPATE").val();
	var respuesta = 'null';
	for (var i = 0, length = respuestas.length; i < length; i++){
		if (respuestas[i].checked){
		  respuesta = respuestas[i].value;
		  break;
		}
	}
	// MANDAMOS LA RESPUESTA SELECCIONADA
	$.ajax({
		url: 'class/TableroPuntaje.php',
		type: 'POST',
		dataType: 'json',
		data: {'functionTablero': 'saveDirect',
			'ID_CONCURSO':concurso,
			'ID_RONDA':ronda,
			'ID_CONCURSANTE':concursante,
			'ID_PREGUNTA': pregunta,
			'ID_RESPUESTA':respuesta,
			'PREGUNTA_POSICION': posicion,
			'PASO':paso,
			'NIVEL_EMPATE':nivelEmpate}
		,success:function(data){
			if(data.estado == 1){
				notFinish = true;
				afterSend();
			}else{
				console.log(data.mensaje)
			}
		  },error:function(error){
			 console.log(error)
		  }
	  });
}

function paso(porError){
	var posicion = $("#PREGUNTA_POSICION").val();
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var pregunta = $("#ID_PREGUNTA").val();
	var concursante = $("#ID_CONCURSANTE").val();
	// MANDAMOS LA RESPUESTA SELECCIONADA
	$.ajax({
		 url: 'class/TableroPuntaje.php',
		 type: 'POST',
		 dataType: 'json',
		 data: {'functionTablero': 'paso',
			  'ID_CONCURSO':concurso,
			  'ID_RONDA':ronda,
			  'ID_CONCURSANTE':concursante,
			  'ID_PREGUNTA': pregunta,
			  'PREGUNTA_POSICION': posicion,
			  'PASO':1
		  },success:function(data){
			 if(data.estado == 1){
			 	if(!porError){
					afterSend();
				}
				notFinish = true;
			  }else{
				console.log(data.mensaje)
			  }
		  },error:function(error){
			 console.log(error)
		  }
	  });
}

function obtenerPreguntaPaso(){
	$.ajax({
		url: 'class/TableroPuntaje.php',
		type: 'GET',
		dataType: 'json',
		data: {'ID_CONCURSO':$("#ID_CONCURSO").val(),
				'ID_RONDA':$("#ID_RONDA").val(),
				'ID_CONCURSANTE':$("#ID_CONCURSANTE").val(),
				'functionTablero':'obtenerPreguntaPaso'},
		success:function(response){
			if(response.estado == 1){
					showPreguntaPaso(response);
			}else {
				alert(response.mensaje);
			}
		},error:function(error){
			console.log(error);
		}
	});
}

function saveRespuestaPaso(){
	var posicion = $("#PREGUNTA_POSICION-paso").val();
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var pregunta = $("#ID_PREGUNTA-paso").val();
	var concursante = $("#ID_CONCURSANTE").val();
	var respuestas = document.getElementsByName("mRespuestaPaso-" + pregunta );
	var respuesta = '';
	for (var i = 0, length = respuestas.length; i < length; i++){
		if (respuestas[i].checked){
		  respuesta = respuestas[i].value;
		  break;
		}
	}
	$.ajax({
		 url: 'class/TableroPaso.php',
		 type: 'POST',
		 dataType: 'json',
		 data: {'functionTableroPaso': 'saveDirect',
			  'ID_CONCURSO':concurso,
			  'ID_RONDA':ronda,
			  'ID_CONCURSANTE':concursante,
			  'ID_PREGUNTA': pregunta,
			  'ID_RESPUESTA':respuesta,
			  'PREGUNTA_POSICION':posicion
		  },success:function(data){
			 if(data.estado == 1){
			 	notFinish1 = true;
				afterSendPaso();
			  }else{
				console.log(data.mensaje)
			  }
		  },error:function(error){
			 console.log(error)
		  }
	  });
}

function afterSend(){
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var concursante = $("#ID_CONCURSANTE").val();
	var pregunta = $("#ID_PREGUNTA").val();
	var categoria = $("#ID_CATEGORIA").val();
	$.ajax({
		url: 'class/TableroPuntaje.php',
		type: 'GET',
		dataType: 'json',
		data: {'ID_CONCURSO':concurso,
				'ID_CATEGORIA':categoria,
				'ID_RONDA':ronda,
				'ID_CONCURSANTE':concursante,
				'PREGUNTA':pregunta,
				'NIVEL_EMPATE':document.getElementById('NIVEL_EMPATE').value,
				'functionTablero':'miPuntajePregunta'},
		success:function(response){
			console.log(response);
			if(response.estado == 1){
				var mensaje  = "<h4>Tu respuesta fue:";
				if(response.puntaje.RESPUESTA_CORRECTA == 1){
					mensaje += " CORRECTA </h4>";
					mensaje += "<img src='image/correcta.png'/>"
				}else{
					mensaje += " INCORRECTA </h4>";
					mensaje += "<img src='image/incorrecta.png'/>"
					paso(true);
				}
				$("#resultado-mi-pregunta").html(mensaje);
				$("#cronometro-content").css("display","none");
				$("#animated text").text(0);
				$("#pregunta p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
				$("#content-respuestas").html("");
				$("#btn-obtener-pr").show(300);
				$("#btn-obtener-pr-paso").show(300);
				$("#btn-paso").hide(300);
			}else{
				alert(response.mensaje);
			}
			if(lanzada == $("#TURNOS_PREGUNTA_CONCURSANTE").val()){
				$("#btn-obtener-pr").hide(300);
				$("#btn-terminar").show(300);
			}
		},
		error: function(error){
			alert("No pudimos mostrarte el resultado de tu pregunta");
			console.log(error);
		}
	});
}

function showPreguntaPaso(response){
	var segundosParaPaso = $("#SEGUNDOS_PASO").val();
	var segundosReales =  segundosParaPaso - response.pregunta[0].TIEMPO_TRANSCURRIDO_PASO;
	if(segundosReales <= 0){
		alert("Vaya! parece que se ha terminado el tiempo de roba puntos :(");
	}else{
		// preparo tambien mi pantalla para el preliminar
		$("body").removeClass('azul');
		$("body").addClass('blanco');
		$("#card-inicio").hide(300);
		$("#pregunta").show(300);
		// ABRIMOS EL MODAL
		$("#mdl-pr-paso").modal({backdrop: 'static', keyboard: false});
		// segundo para cada pregunta
		var segundos = $("#segundos_ronda").val();
		//cambiamos la vista
		$("#pregunta-paso").show(300);
		$("#resultado-mi-pregunta-paso").html("");
		// seteamos los valores de lap regunta a mostrar
		$("#pregunta-paso p").text(response.pregunta[0].PREGUNTA);
		$("#ID_PREGUNTA-paso").val(response.pregunta[0].ID_PREGUNTA);
		$("#PREGUNTA_POSICION-paso").val(response.pregunta[0].PREGUNTA_POSICION);
		// mostramos las respuestas posibles para la pregunta
		var respuestas = response.pregunta.respuestas;
		var incisos =['a','b','c','d'];
		var contenido = "<div class='row'>";
		for(var x= 0; x < respuestas.length ; x++){
			contenido += "<div class='col-md-3 centrado text-answer'>";
			contenido += "<button type='button' class='btn-answer' onclick='eligeIncisoPaso(this)'>"+incisos[x]+"</button><br><br>";
			contenido += "<input type='radio' name='mRespuestaPaso-"+response.pregunta[0].ID_PREGUNTA+"' value='"+ respuestas[x].ID_RESPUESTA +"' style='display:none'/>";
			if(respuestas[x].ES_IMAGEN == 1){
				contenido += "<img src='image/respuestas/" + respuestas[x].RESPUESTA + "'/>";
			}else{
				contenido += respuestas[x].RESPUESTA;
			}
			contenido += "</div>";
		}
		contenido += "</div>";
		$("#content-respuestas-paso").html(contenido);
		cronometroPaso(segundosReales,function(){},function(){saveRespuestaPaso();});
	}
	
}

function eligeIncisoPaso(boton){
	$(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
	$(boton).addClass('btn-checked');
	$($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
	saveRespuestaPaso(0);
	notFinish1 = true;
}

function afterSendPaso(){
	notFinish1 = true;
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var concursante = $("#ID_CONCURSANTE").val();
	var pregunta = $("#ID_PREGUNTA-paso").val();
	var categoria = $("#ID_CATEGORIA").val();
	$.ajax({
		url: 'class/TableroPaso.php',
		type: 'GET',
		dataType: 'json',
		data: {'ID_CONCURSO':concurso,
				'ID_CATEGORIA':categoria,
				'ID_RONDA':ronda,
				'ID_CONCURSANTE':concursante,
				'PREGUNTA':pregunta,
				'functionTableroPaso':'miPuntajePregunta'},
		success:function(response){
			console.log(response);
			if(response.estado == 1){
				var mensaje  = "<h4>Tu respuesta fue:";
				if(response.puntaje.RESPUESTA_CORRECTA == 1){
					mensaje += " CORRECTA </h4>";
					mensaje += "<img src='image/correcta.png'/>"
				}else{
					mensaje += " INCORRECTA </h4>";
					mensaje += "<img src='image/incorrecta.png'/>"
				}
				// motramos el marcador de la pregunta en la pantalla principal
				$("#resultado-mi-pregunta").html(mensaje);
				$("#cronometro-content-paso").css("display","none");
				$("#animated-paso text").text(0);
				$("#pregunta p").text("Termino tu pregunta de roba puntos");
				$("#content-respuestas-paso").html("");
				$("#content-respuestas").html("");
				$("#btn-paso").hide(300);
				//cerramos el modal de robapuntos
				$('#mdl-pr-paso').modal('hide');
			}else{
				alert(response.mensaje);
			}

			if(lanzada == $("#TURNOS_PREGUNTA_CONCURSANTE").val()){
				$("#btn-obtener-pr").hide(300);
				$("#btn-terminar").show(300);
			}
		},
		error: function(error){
			alert("No pudimos mostrate el resultado de tu pregunta");
			console.log(error);
		}
	});
}

function terminarParticipacion(){
	$.ajax({
		url: 'class/Concursante.php',
		type: 'GET',
		dataType: 'json',
		data: {"ID_CONCURSO": $("#ID_CONCURSO").val() , "functionConcursante":"terminarParticipacionGrupal"},
		success:function(response){
			if(response.estado == 1){
				if(response.termino_ronda == 1){
					window.location.replace('inicio_desempate');
				}else{
					alert("Aun o termina tu la ronda por completo.");
				}
			}else{
				alert("Vuelve a intentar :( ");
				console.log('Fallo el listener de cambio de ronda: ' + response.mensaje);
			}
		},
		error:function(error){
			alert("Vuelve a intentar :( ");
			console.log(error);
		}
	});
}