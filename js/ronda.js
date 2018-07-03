/**
 * Accede al archivo de la ronda indicada
 * @param  integer ronda [description]
 */
function accederRonda(ronda){
	 switch(ronda){
		case 1:
			window.location.replace("individual1");
		break;
		case 2:
			window.location.replace("individual2");
		break;
		case 3:
			window.location.replace("individual_desempate");
		break;
		case 4:
			window.location.replace("grupal1");
		break;
		case 5:
			window.location.replace("grupal2");
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
 * Inicializa el listener de cambio de ronda
 * @param  integer rondaActual 
 */
function initListenerCambioRonda(rondaActual,categoriaActual){
	try {
		$ = $jq;
	} catch(e) {
		console.log(e);
	}
	$.ajax({
		url: 'class/listeners/lst_cambio_ronda.php',
		type: 'GET',
		dataType: 'json',
		data: {"rondaActual": rondaActual,'categoriaActual':categoriaActual},
		success:function(response){
			if(response.estado == 1){
				if(response.termino == 1){
					if(response.empate == 1){
						window.location.replace('inicio_desempate');
					}else if(response.empate == 0){
						window.location.replace('concurso_finalizado');
					}
					
				}else{
					accederRonda(response.ronda);	
				}
			}
		},
		error:function(error){
			alert("Ocurrio un error inesperado");
		}
	})
}