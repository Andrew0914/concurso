var lanzada = 0;
function obtenerPregunta(){
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
				}
			}else {
				alert(response.mensaje + " VUELVE A INTENTAR");
			}
		},error:function(error){
			console.log(error);
		}
	});
}

function showPregunta(response){
	// segundo para cada pregunta
	var segundos = $("#segundos_ronda").val();
	//cambiamos la vista
	$("body").removeClass('azul');
	$("body").addClass('blanco');
	$("#card-inicio").hide(300);
	$("#pregunta").show(300);
	$("#resultado-mi-pregunta").html("");
	// seteamos los valores de lap regunta a mostrar
	$("#pregunta #text-pregunta").text(response.pregunta[0].PREGUNTA);
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
	cronometro(segundos,null,function(){paso();});
}

function eligeInciso(boton){
	// reseteamos los estilos de los no checados
	 $(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
	 $(boton).addClass('btn-checked');
	 $($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
	 saveRespuesta(0);
}

function saveRespuesta(paso){
	var concurso = $("#ID_CONCURSO").val();
	var ronda = $("#ID_RONDA").val();
	var pregunta = $("#ID_PREGUNTA").val();
	var concursante = $("#ID_CONCURSANTE").val();
	var respuestas = document.getElementsByName("mRespuesta-" + pregunta );
	var respuesta = '';
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
			  'PASO':paso
		  },success:function(data){
			 if(data.estado == 1){
				afterSend();
			  }else{
				console.log(data.mensaje)
			  }
		  },error:function(error){
			 console.log(error)
		  }
	  });
}

function paso(){
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
			  'PASO':1
		  },success:function(data){
			 if(data.estado == 1){
				afterSend();
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
				alert(response.mensaje + " VUELVE A INTENTAR");
			}
		},error:function(error){
			console.log(error);
		}
	});
}

function saveRespuestaPaso(){
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
			  'ID_RESPUESTA':respuesta
		  },success:function(data){
			 if(data.estado == 1){
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
				}
				$("#resultado-mi-pregunta").html(mensaje);
				$("#cronometro-content").css("display","none");
				$("#animated text").text(0);
				$("#pregunta p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
				$("#content-respuestas").html("");
			}else{
				alert(response.mensaje);
			}
		},
		error: function(error){
			alert("No pudimos mostrate el resultado de tu pregunta");
			console.log(error);
		}
	});
}

function showPreguntaPaso(){
	$("#mdl-pr-paso").modal();
	// segundo para cada pregunta
	var segundos = $("#segundos_ronda").val();
	//cambiamos la vista
	$("body").removeClass('azul');
	$("body").addClass('blanco');
	$("#pregunta-paso").show(300);
	$("#resultado-mi-pregunta-paso").html("");
	// seteamos los valores de lap regunta a mostrar
	$("#pregunta-paso #text-pregunta-paso").text(response.pregunta[0].PREGUNTA);
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
	cronometroPaso(5,null,function(){saveRespuestaPaso();});
}

function eligeIncisoPaso(){
	$(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
	$(boton).addClass('btn-checked');
	$($(boton).siblings('input[type=radio]')[0]).prop('checked',true);
	saveRespuesta(0);
}

function afterSendPaso(){
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
				'NIVEL_EMPATE':document.getElementById('NIVEL_EMPATE').value,
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
				$("#resultado-mi-pregunta-paso").html(mensaje);
				$("#cronometro-content-paso").css("display","none");
				$("#animated-paso text").text(0);
				$("#pregunta-paso p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
				$("#content-respuestas-paso").html("");
			}else{
				alert(response.mensaje);
			}
		},
		error: function(error){
			alert("No pudimos mostrate el resultado de tu pregunta");
			console.log(error);
		}
	});
}