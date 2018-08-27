$(document).ready(function(){
    // AJUSTE DEL COMBO DE CONCURSO
    $('#ID_CONCURSO').change(function(){
        $("#widthTempOption").html($('#ID_CONCURSO option:selected').text());
        $(this).width($("#selectTagWidth").width()); 
    });
    // AJUSTE DEL COMBO DE CONCURSANTES
    $('#CONCURSANTE').change(function(){
        $("#widthTempOption").html($('#CONCURSANTE option:selected').text());
        $(this).width($("#selectTagWidth").width()); 
    });
});