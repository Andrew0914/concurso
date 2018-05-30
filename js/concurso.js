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
            window.location.replace("/");
          }
        },
        error : function(error){
        	alert("Oops! ocurrio un error inesperado, actualiza la pagina e intenta de nuevo");
          	console.log(error);
        }
     });
}

function iniciarConcurso(formulario){
	$.ajax({
        type : 'POST',
        url  : 'class/Concurso.php',
        data :$(formulario).serialize()+"&functionConcurso=iniciarConcurso",
        dataType: "json",
        success : function(response){
          if(response.estado == 1){
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