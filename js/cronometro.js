var stopExecPerSecond = false;
var notFinish = false;
/**
 * Funcion que inicia un cronometro a partir de un svg con los segundos indicados
 * @param  {int} segundos 
 */
function cronometro(segundos, callbackPerSecond, finishCallback){
	stopExecPerSecond= false;
	notFinish = false;
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
	  }
	});

	var s = Snap('#animated');
	var progress = s.select('#progress');
	progress.attr({strokeDasharray: '0, 251.2'});
	Snap.animate(0,251.2, function( value ) {
	    progress.attr({ 'stroke-dasharray':value+',251.2'});
	}, ms);
	// iniciamos los coronometros reales, el resto es una animacion
	initTimerPerSecond(segundos,callbackPerSecond,finishCallback);
}

/**
 * Es el temporizador que ejecutara la funcion cada segundo
 * @param  integer segundos       
 * @param  callback functionPerSecond 
 */
function initTimerPerSecond(segundos,functionPerSecond, finishFunction){
	var contador = 1;
	var timerPerSecond = setInterval(function(){
		console.log(contador);
		if((contador % 2) == 0){
			functionPerSecond();
		}

		if(contador == segundos && !notFinish){
			finishFunction();
		}

		if(contador == segundos || stopExecPerSecond){
			clearInterval(timerPerSecond);
		}

		contador ++;
	},1000);
}
