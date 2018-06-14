var stopExecPerSecond = false;
/**
 * Funcion que inicia un cronometro a partir de un svg con los segundos indicados
 * @param  {int} segundos 
 */
function cronometro(segundos, callbackPerSecond, finishCallback){
	// usando variable sin conflictos por si uso prototype
	try {
		$ = $jq;
	} catch(e) {
		console.log(e);
	}
	$("#cronometro-content").css("display","block");
	$("#animated text").text(segundos);
	var ms = segundos * 1000;
	var count = $(('#cronometro'));
	var final = false;
	var aSegundos =[0];
	$({ Counter: 0 }).animate({ Counter: count.text() }, {
	  duration: ms,
	  easing: 'linear',
	  step: function (a,b) {
	  	
	  	var real = Math.ceil(this.Counter);
	  	var display = "00:"+real;
	  	if(real < 10){
	  		display = "00:0"+real;
	  	}
	    count.text(display);
	    if(!stopExecPerSecond && typeof callbackPerSecond === 'function' && (real % 2) == 0 && segundoReady(aSegundos,real)){
	    	callbackPerSecond();
	    	aSegundos.push(real);
	    }   
	    if( real == segundos  && typeof finishCallback === 'function' && !final){
	    	finishCallback();
	    	final = true;
	    }
	  }
	});
	var s = Snap('#animated');
	var progress = s.select('#progress');
	progress.attr({strokeDasharray: '0, 251.2'});
	Snap.animate(0,251.2, function( value ) {
	    progress.attr({ 'stroke-dasharray':value+',251.2'});
	}, ms);
}

function segundoReady(aSegundos,segundo){
	var listo = true;
	for(var popo = 0; popo<= aSegundos.length ; popo++){
		if(aSegundos[popo] == segundo){
			listo = false;
			break;
		}
	}
	return listo;
}