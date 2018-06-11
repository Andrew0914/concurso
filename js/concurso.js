/**
 * Genera el html para los concursantes indicados del concurso
 */
function generaConcursantes(){
	var cantidad = $("#CANTIDAD_PARTICIPANTES").val();
	var concursantesData = "";
	for(var i=1; i<=cantidad ; i++){
		concursantesData+= "<tr>";
		concursantesData+= "<td><input type='text' name='CONCURSANTE[]' class='form-control' placeholder='Concursante'></td>";
		concursantesData+= "<td><input type='text' name='PASSWORD[]' class='form-control' placeholder='Password'></td>";
		concursantesData+= "<td><input type='numeric' name='CONCURSANTE_POSICION[]' class='form-control' readonly value='"+i+"'></td>";
		concursantesData+= "</tr>";
	}
	$("#tbl-concursantes tbody").html(concursantesData);
	$("#btn_generar_concursantes").hide(300);
	$("#btn_generar_concurso").show(400);
}

/**
 * Realiza la peticion para la generacion del concurso y concursantes
 * @param  {[form]} formulario [objeto del formulario]
 */
function generarConcurso(formulario){
	$.ajax({
        type : 'POST',
        url  : 'class/Concurso.php',
        data :$(formulario).serialize()+"&functionConcurso=generaConcurso",
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
            alert(response.mensaje);
          	window.location.replace("panel");
          }else{
          	alert(response.mensaje);
            window.location.replace("crear");
          }
        },
        error : function(error){
        	alert("Oops! ocurrio un error inesperado, actualiza la pagina e intenta de nuevo");
          	console.log(error);
        }
     });
}

/**
 * Inicia el concurso
 * @param  {object} formulario 
 */
function iniciarConcurso(formulario,boton){
	$.ajax({
        type : 'POST',
        url  : 'class/Concurso.php',
        data :$(formulario).serialize()+"&functionConcurso=iniciarConcurso",
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
            $(boton).css('display','none');
            $("#btnObtenerPuntaje").css('display', 'block');
          	alert(response.mensaje);
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

/**
 * Es el inicio de sesion del moderador para entrar al panel del concurso indicado
 */
function irConcurso(){
  var concurso = $("#ID_CONCURSO").val();
  if(concurso != ""){
    $.get('class/Concurso.php?functionConcurso=irConcurso&concurso='+concurso,
      function(data, textStatus, xhr) {
        if(data.estado == 1){
          alert(data.mensaje);
          window.location.replace("panel");
        }
    },'json');
  }else{
    alert("Debes seleccionar un concurso");
  }
  
}

/**
 * Obtiene y despliega el puntaje en lso tableros
 * @param  {int} concurso 
 * @param  {int} ronda    
 */
function obtenerPuntaje(concurso,ronda){
  $.get('class/TableroPuntaje.php?functionTablero=getTableroDisplay&ID_CONCURSO='+concurso + "&ID_RONDA="+ronda, 
    function(data, textStatus, xhr) {
      if(data.estado == 1){
        var contenido = "";
        var tablero = data.tablero;
        for(var s=0; s< tablero.length ; s++){
          contenido += "<tr>";
          contenido += "<td>" + tablero[s].CONCURSANTE+"</td>";
          contenido += "<td>" + tablero[s].PREGUNTA_POSICION+"</td>";
          if(tablero[s].RESPUESTA == null && tablero[s].INCISO == null){
            contenido += "<td>No respondió</td>";
          }else{
            contenido += "<td>" + tablero[s].INCISO+") ";
            if(tablero[s].ES_IMAGEN == 1){
              contenido += "<img src='image/respuestas/"+ tablero[s].RESPUESTA+ "'/></td>";
            }else{
              contenido += tablero[s].RESPUESTA+ "</td>";
            }
          }
          contenido += "<td>" + tablero[s].PASO_PREGUNTA+"</td>";
          contenido += "<td>" + tablero[s].PUNTAJE+"</td>";
          contenido +="</tr>";
        }
        $("#tbl-puntaje tbody").html(contenido);
        var topPos =$("#tbl-puntaje tbody tr:last").offset();
        $("#divtablero").scrollTop(topPos.top);
        // desplegamos los primeros tres lugares
        obtenerMejores(concurso,ronda);
      }else{
        console.log(data.mensaje);
      }
  },'json');
}

/**
 * Obtiene el tablero de las mejores posiciones y acumulado de puntos
 * @param  {int} concurso [description]
 * @param  {int} ronda    [description]
 */
function obtenerMejores(concurso,ronda){
  $.get('class/TableroPuntaje.php?functionTablero=getMejoresPuntajes&ID_CONCURSO='+concurso + "&ID_RONDA="+ronda, 
    function(data, textStatus, xhr) {
      if(data.estado == 1){
        var contenido = "";
        var mejores = data.mejores;
        for(var s=0; s< mejores.length ; s++){
          contenido += "<tr>";
          var medalla ="";
          if(s==0){
            medalla = "<img src='image/gold_medal.png'>";
          }else if(s==1){
            medalla = "<img src='image/silver_medal.png'>";
          }else if(s==2){
            medalla = "<img src='image/bronze_medal.png'>";
          }
          contenido += "<td>" + medalla +"</td>";
          contenido += "<td>" + mejores[s].CONCURSANTE+"</td>";
          contenido += "<td>" + mejores[s].totalPuntos+"</td>";
          contenido +="</tr>";
        }
        $("#tbl-mejores tbody").html(contenido);
      }else{
        console.log(data.mensaje);
      }
  },'json');
}

function setRondas(etapa){
  var idEtapa = $(etapa).val();
  $.get('class/Rondas.php?etapa='+idEtapa+"&functionRonda=getRondas",
    function(data) {
      var rondas = data.rondas;
      var content = "<option value=''>Selecciona una ronda</option>";
      for(var d=0; d<rondas.length; d++){
        content += "<option value='" + rondas[d].ID_RONDA;
        content += "'>" + rondas[d].RONDA + "</option>";
      }
      $("#ID_RONDA").html(content);
  },'json');
}