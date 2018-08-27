// auto ajusta el select de los concursos ya que peuden ser largos
$(document).ready(function(){
    $('#ID_CONCURSO').change(function(){
        $("#widthTempOption").html($('#ID_CONCURSO option:selected').text());
        $(this).width($("#selectTagWidth").width()); 
    });
});