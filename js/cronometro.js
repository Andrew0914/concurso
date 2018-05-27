/**
 * Funcion que inicia un cronometro a partir de un svg con los segundos indicados
 * @param  {int} segundos 
 */
function cronometro(segundos){
	$("#animated text").text(segundos);
	var ms = segundos * 1000;
	var count = $(('#cronometro'));
	$({ Counter: 0 }).animate({ Counter: count.text() }, {
	  duration: ms,
	  easing: 'linear',
	  step: function () {
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
}