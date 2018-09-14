var stopExecPerSecond1 = false;
var notFinish1 = false;
var timerCronometroPaso = null;
/**
 * Funcion que inicia un cronometro a partir de un svg con los segundos indicados
 * @param  {int} segundos 
 */
function cronometroPaso(segundos, callbackPerSecond, finishCallback){
	stopExecPerSecond1= false;
	notFinish1 = false;
	// usando variable sin conflictos por si uso prototype
	try {
		$ = $jq;
	} catch(e) {
		//console.log(e);
	}
	$("#cronometro-content-paso").css("display","flex");
	$("#animated-paso text").text(segundos);
	var ms = segundos * 1000;
	var count = $(('#cronometro-paso'));
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

	var s = Snap('#animated-paso');
	var progress = s.select('#progress-paso');
	progress.attr({strokeDasharray: '0, 251.2'});
	Snap.animate(0,251.2, function( value ) {
	    progress.attr({ 'stroke-dasharray':value+',251.2'});
	}, ms);
	// iniciamos los coronometros reales, el resto es una animacion
	initTimerPerSecond1(segundos,callbackPerSecond,finishCallback);
}

/**
 * Es el temporizador que ejecutara la funcion cada segundo
 * @param  integer segundos       
 * @param  callback functionPerSecond 
 */
function initTimerPerSecond1(segundos,functionPerSecond, finishFunction){
	var contador = 1;
	timerCronometroPaso = setInterval(function(){
		//if((contador % 2) == 0){
			functionPerSecond();
		//}

		if(contador == segundos && !notFinish1){
			finishFunction();
		}

		if(contador == segundos || stopExecPerSecond1){
			clearInterval(timerPerSecond);
		}

		contador ++;
	},1000);
}
