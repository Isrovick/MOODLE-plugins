$( document ).ready(function() {
    $('input[name="gsearch"]').prop('disabled', true);
    
 });
 


$('select[name^="useroption"]').change(function(){

    if(this.value=='+'){
        $('input[name="gsearch"]').prop('disabled', false);
    }
    else{
        $('input[name="gsearch"]').prop('disabled', true);
        $('div[name^="rb_"]').remove();
    }

});

$( 'input[name="gsearch"]').keyup(function() {  
    load_form(this.value);
});


function load_form(key){
    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var baserurl=re.exec(window.location.href);
    var courseid = $('input[name="cid"]').val();
    
    console.log(baserurl);
    baserurl=baserurl[0];

   return $.ajax({
            url : baserurl+'/teacher_mail_ajax.php',
            data : { payload: key, cid:courseid },
            type : 'POST',
            dataType : 'json',
            success : function(json) {
                $('.rmv').remove();
                //console.log(json);
                if(json!=false){
                    var html="";
                   
                    for(var element in json){
                        element=json[element];

                        html+=  '<div  name="search_'+element.userid+'" class="col-md-auto rmv" > '+
                                    '<label class="col-md-3"></label>'+
                                    '<label style="background-color: lightgrey" name="uname_'+element.userid+'">'+element.data +' - '+element.firstname +', '+element.lastname+'</label> '+
                                '</div>'; 
    
                    }

                    $('fieldset[name="list"]').append(html);
                    add_list();
                }
            },
            error : function(xhr, status) {
                alert('7 Disculpe, ocurrio un problema');
            },
            complete : function(xhr, status) {
              
            }
        });   

}

function add_list(){
    $('div[name^="search_"]').click(function(){
        var re = /search_([0-9]*)/ig;
        var rer=re.exec($(this).attr('name'));
        id=rer[1];
        var fln=$('label[name="uname_'+id+'"]').text();
        //console.log(fln);
        $('input[name="gsearch"]').val('');
        $('.rmv').remove();
        var html='<div class="col-md-auto " name="rb_'+id+'"> '+
                    '<label class="col-md-3"></label>'+
                    '<label style="background-color: lightblue">'+fln+'</label> '+
                    '<button type="button" name="bx_'+id+'" class="btn-sx btn-danger">X</button>'+
                    '<input type="hidden" id="userid_'+id+'" name="userid_'+id+'" value="'+id+'"> '+
                 '</div>'; 

         if(!$('div[name="rb_'+id+'"]').length) {
            $('fieldset[name="users"]').append(html);

            $('button[name^="bx_"]').click(function(){
                $(this).parent().remove();
            });

         }
       

     });     
}

$('form[id^="mform1_"]').submit(function(event) {
       
    var ano_id=$('select[name="ano_id"]').val();
    var userid=$('input[name^="userid_"]').length;
    var subject=$('input[name="subject"]').val();
    var body=$('textarea[name="contenido\[text\]"]').val();
    
    if($('select[name="useroption"]').children('option:selected').val()==-1){
        alert("¡Debe Seleccionar al una opcion a enviar las notificaciones!");
        return false;
    } 
    if($('select[name="useroption"]').children('option:selected').val()=='+' && !userid){      
            alert("¡Debe ingresar el/los destinarios!");
            return false;  
    }
    if(!subject){
        alert("¡Debe ingresar el asunto del correo!");
        return false;
    }
    if(!body){
        alert("¡Debe ingresar el contenido del correo!");
        return false;
    }


});