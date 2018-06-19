/**
 * Inicializa el listener de cambio de ronda
 * @param  integer rondaActual 
 */
function initListenerCambioRonda(rondaActual){
	$.ajax({
		url: 'class/listeners/lst_cambio_ronda.php',
		type: 'GET',
		dataType: 'json',
		data: {"rondaActual": rondaActual},
		success:function(response){
			if(response.estado == 1){
				accederRonda(response.ronda);
			}
		},
		error:function(error){
			alert("Ocurrio un error inesperado");
		}
	})
}
/**
 * Accede al archivo de la ronda indicada
 * @param  integer ronda [description]
 */
function accederRonda(ronda){
	 switch(ronda){
		case 1:
			window.location.replace("individual_ronda1");
		break;
		case 2:
			window.location.replace("individual_ronda2");
		break;
		case 3:
			window.location.replace("individual_desempate");
		break;
		case 4:
			window.location.replace("grupal_ronda1");
		break;
		case 5:
			window.location.replace("grupal_ronda2");
		break;
		case 6:
			window.location.replace("grupal_desempate");
		break;
		default:
			alert("No pudimos redirigirte a tu concurso, intenta de nuevo");
		}

		window.onbeforeunload = function(e){
			return null;
		};
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