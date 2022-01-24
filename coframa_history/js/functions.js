$( document ).ready(function() {

    
 });

 $('form[id^="mform1_"]').submit(function(event) {
    console.log("here");
   
    if($('select[name="nivel"]').children('option:selected').val()==-1){
        alert("¡Debe Seleccionar un año!");
        return false;
    }
    if($('select[name="seccion"]').children('option:selected').val()==-1){
        alert("¡Debe Seleccionar una seccion!");
        return false;
    }
    if($('select[name="periodo"]').children('option:selected').val()==-1){
        alert("¡Debe Seleccionar un periodo!");
        return false;
    }
    $('input[id^="id_lapso"]').each(function(){
        if(!$(this).is(":checked")){
           return false;
        }
    });

});
 