// LISTENER PARA ESCUCHAR EL CAMBIO DE PREGUNTA
var lastLanzada = false;
var Comet = Class.create();
  Comet.prototype = {
	 lanzada: 0,
	 finish: false,
	 url: 'class/listeners/lst_pregunta_lanzada.php',
	 noerror: true,
	 initialize: function() { },
	 connect: function(){
	 	var categoria = document.getElementById('ID_CATEGORIA').value;
	 	var esDesempate = document.getElementById('IS_DESEMPATE').value;
		this.ajax = new Ajax.Request(this.url, {
		  method: 'get',
		  parameters: { 'lanzada' : this.lanzada , 
						'ID_CONCURSO': document.getElementById("ID_CONCURSO").value,
						'ID_RONDA': document.getElementById("ID_RONDA").value,
						'ID_CATEGORIA': categoria,
						'IS_DESEMPATE': document.getElementById('IS_DESEMPATE').value,
						'NIVEL_EMPATE': document.getElementById('NIVEL_EMPATE').value},
		  onSuccess: function(transport) {
			 var response = transport.responseText.evalJSON();
			 if(response.tiempo_muerto != 1 ){
			 	this.comet.lanzada = response['lanzada'];
			 	this.comet.handleResponse(response);
			 	this.comet.noerror = true;
			 }
		  },
		  onComplete: function(transport) {
		  	var response = transport.responseText.evalJSON();
		  	if(response.tiempo_muerto != 1){
		  		if(response.todas_lanzadas != 1){
			  		if (!this.comet.noerror){
						setTimeout(function(){ comet.connect() }, 1000); 
					}
					else{
						this.comet.connect();
						this.comet.noerror = false;
				  	}
			  	}
		  	}else{
		  		setTimeout(function(){ comet.connect() }, 1500); 
		  	}
		  }
		});
		this.ajax.comet = this;
	 },
	 disconnect: function(){
		return true;
	 },
	 handleResponse: function(response){
	 	if(!lastLanzada){
	 		showPregunta(response);
	 	}else{
	 		//console.log('Baia baia quiciste ponerme de nuevo la ultima');
	 	}
	 	if(this.lanzada >=  document.getElementById('PREGUNTAS_POR_CATEGORIA').value && !lastLanzada){
 			finalizaRonda(response.concurso);
 			lastLanzada = true;
	 	}	 	
	}
}

var comet = new Comet();
comet.connect();

/**
 * Muestra la pregunta y las respuestas correspondientes
 * @param  {json} response [description]
 */
function showPregunta(response){
	//console.log("Mostrando pregunta");
	// segundo para cada pregunta
	var segundos = $jq("#segundos_ronda").val();
	//cambiamos la vista
	$jq("body").removeClass('azul');
	$jq("body").addClass('blanco');
	$jq("#card-inicio").hide(300);
	$jq("#pregunta").show(300);
	$jq("#resultado-mi-pregunta").html("");
	// seteamos los valores de lap regunta a mostrar
	$jq("#pregunta p").text(response.pregunta[0].PREGUNTA);
	$jq("#ID_PREGUNTA").val(response.pregunta[0].ID_PREGUNTA);
	$jq("#PREGUNTA_POSICION").val(response.pregunta[0].PREGUNTA_POSICION);
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

	$jq("#content-respuestas").html(contenido);
	var fnSegundo = function(){
			todosContestaron();
	};
	var fnTermino = function(){
		sendRespuesta();
	};
	cronometro(segundos,fnSegundo,fnTermino);
}

/**
 * Selecciona la respuesta que sera enviada
 * @param  {button} boton [opcion oprimida]
 */
function eligeInciso(boton){
	 // reseteamos los estilos de los no checados
	 $jq(boton).parent().parent().children('div').children('button').removeClass('btn-checked');
	 $jq(boton).addClass('btn-checked');
	 // checamos el radio oculto
	 $jq($jq(boton).siblings('input[type=radio]')[0]).prop('checked',true);
	 if($jq($jq(boton).siblings('input[type=radio]')[0]).prop('checked')){
	 	sendPreRespuestas(0);
	 }else{
	 	eligeInciso(boton);
	 }
}

/**
 * Rregistra la accion previa a mandar la respuesta , para validar que todos hayan contestado
 * @param  integer final 
 */
function sendPreRespuestas(final){
	var posicion = $jq("#PREGUNTA_POSICION").val();
	var concurso = $jq("#ID_CONCURSO").val();
	var ronda = $jq("#ID_RONDA").val();
	var concursante = $jq("#ID_CONCURSANTE").val();
	var pregunta = $jq("#ID_PREGUNTA").val();
	$jq.post('class/TableroPuntaje.php', 
		{'functionTablero': 'preRespuesta',
			'ID_CONCURSO':concurso,
			'ID_RONDA':ronda,
			'ID_CONCURSANTE':concursante,
			'PREGUNTA_POSICION':posicion,
			'PREGUNTA': pregunta,
			'final':final,
			'NIVEL_EMPATE':document.getElementById('NIVEL_EMPATE').value
		},
		function(data, textStatus, xhr) {
			if(data.estado == 0){
				console.log("ejecucion al recibir estado 0 pre");
				sendPreRespuestas(final);
			}else{
				//console.log('Se mando pre respuesta');
			}
	},'json');
}

/**
 * Lanza la peticion para saber si todos los participantes contestaron
 */
function todosContestaron(){
	var concurso = $jq("#ID_CONCURSO").val();
	var ronda = $jq("#ID_RONDA").val();
	var pregunta = $jq("#ID_PREGUNTA").val();
	$jq.get('class/TableroPuntaje.php?functionTablero=todosContestaron&ID_CONCURSO='
			+concurso+'&ID_RONDA='+ronda+'&ID_PREGUNTA='+pregunta+'&NIVEL_EMPATE=' + document.getElementById('NIVEL_EMPATE').value, 
		function(data) {
			if(data.estado == 1){
				stopExecPerSecond= true;
				notFinish = true;
				sendRespuesta();
			}
	},'json');
}

/**
 * Manda la respuesta final del participante
 */
function sendRespuesta(){
	var concurso = $jq("#ID_CONCURSO").val();
	var ronda = $jq("#ID_RONDA").val();
	var pregunta = $jq("#ID_PREGUNTA").val();
	var concursante = $jq("#ID_CONCURSANTE").val();
	var respuestas = document.getElementsByName("mRespuesta-" + pregunta );
	var respuesta = '';

	for (var i = 0, length = respuestas.length; i < length; i++){
		if (respuestas[i].checked){
		  respuesta = respuestas[i].value;
		  break;
		}
	}
	// SI NO EXISTE OPCION SELECCIONADA
	if(respuesta == ''){
		// solo mandamso la pre respuesta (con la respuesta nula)
		sendPreRespuestas(1);
		afterSend();
	}else{
		// MANDAMOS LA RESPUESTA SELECCIONADA
	  $jq.ajax({
		 url: 'class/TableroPuntaje.php',
		 type: 'POST',
		 dataType: 'json',
		 data: {'functionTablero': 'saveRespuesta',
			  'ID_CONCURSO':concurso,
			  'ID_RONDA':ronda,
			  'ID_CONCURSANTE':concursante,
			  'ID_PREGUNTA': pregunta,
			  'ID_RESPUESTA':respuesta,
			  'NIVEL_EMPATE':document.getElementById('NIVEL_EMPATE').value
		  },success:function(data){
			 if(data.estado == 1){
				afterSend();
				stopExecPerSecond= true;
				notFinish = true;
			  }else{
				console.log(data.mensaje)
			  }
		  },error:function(a,b,c){
			 console.log(a);
			 console.log(b);
			 console.log(c);
		  }
	  });

	}
}

/**
 * Resetea la pantalla para esperar y muestra el resutlado de la ultima pregunta
 */
function afterSend(){
	var concurso = $jq("#ID_CONCURSO").val();
	var ronda = $jq("#ID_RONDA").val();
	var concursante = $jq("#ID_CONCURSANTE").val();
	var pregunta = $jq("#ID_PREGUNTA").val();
	var categoria = $jq("#ID_CATEGORIA").val();
	$jq.ajax({
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
			if(response.estado == 1){
				var mensaje  = "<h4>Tu respuesta fue:";
				if(response.puntaje.RESPUESTA_CORRECTA == 1){
					mensaje += " CORRECTA </h4>";
					mensaje += "<img src='image/correcta.png'/>"
				}else{
					mensaje += " INCORRECTA </h4>";
					mensaje += "<img src='image/incorrecta.png'/>"
				}
				$jq("#resultado-mi-pregunta").html(mensaje);
				$jq("#cronometro-content").css("display","none");
				$jq("#animated text").text(0);
				$jq("#pregunta p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
				$jq("#content-respuestas").html("");
			}else{
				$jq("#resultado-mi-pregunta").html(response.mensaje);
				$jq("#cronometro-content").css("display","none");
				$jq("#animated text").text(0);
				$jq("#pregunta p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
				$jq("#content-respuestas").html("");
			}
		},
		error: function(error){
			$jq("#resultado-mi-pregunta").html("No pudimos mostrarte el marcador de tu pregunta :( ");
			$jq("#cronometro-content").css("display","none");
			$jq("#animated text").text(0);
			$jq("#pregunta p").text("Termino la pregunta, por favor espera a que lance la siguiente el moderador");
			$jq("#content-respuestas").html("");
			console.log("error mi marcador");
			console.log(error);
		}
	});
}



/**
 * Finaliza la ronda inicializando el listener de cambio de ronda
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function finalizaRonda(response){
	initListenerCambioRonda($jq("#ID_RONDA").val(), $jq("#ID_CATEGORIA").val());
}
