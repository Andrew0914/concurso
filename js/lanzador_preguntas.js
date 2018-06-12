/**
 * Dispara el dialogo con la pregunta para lanzarla
 * @param  {[int]} pregunta   
 * @param  {[int]} idPregunta 
 * @param  {[int]} idGenerada 
 */
function leer(pregunta,idPregunta,idGenerada){
    $("#mdl-leer-pregunta").modal();
    $("#p-pregunta").text(pregunta);
}