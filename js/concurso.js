/**
 * Genera el html para los concursantes indicados del concurso
 */
function generaConcursantes(){
	var cantidad = $("#CANTIDAD_PARTICIPANTES").val();
  if(!isNaN(cantidad)){
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
  }else{
    alert("Por favor pon una cantidad real de concursantes");
  }	
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
 * Setea las rondas en el select por etapa
 * @param {[select]} etapa 
 */
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

function setCategorias(etapa){
    var idEtapa = etapa.value;
    $.get('class/Categorias.php?ID_ETAPA='+idEtapa+"&functionCategorias=getCategoriasPermitidas",
    function(data) {
      var categorias = data.categorias;
      var content = "<option value=''>Selecciona una Categoria para iniciar</option>";
      for(var d=0; d<categorias.length; d++){
        content += "<option value='" + categorias[d].ID_CATEGORIA;
        content += "'>" + categorias[d].CATEGORIA + "</option>";
      }
      $("#ID_CATEGORIA").html(content);
  },'json');
}