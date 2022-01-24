$( document ).ready(function() {
    $('input[name="gsearch"]').prop('disabled', true);
    
 });
 


$('select[name^="materias_id"]').change(function(){

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
    var courseid = $("#id_courseid").val();
    

    baserurl=baserurl[0].replace('promo','infoprofe');

   return $.ajax({
            url : baserurl+'/inform_primini_ajax.php',
            data : { payload: key},
            type : 'POST',
            dataType : 'json',
            success : function(json) {
                $('.rmv').remove();

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
    console.log("here");
    var flag=false;
    if($('select[name="materias_id"]').children('option:selected').val()==-1){
        alert("¡Debe Seleccionar al una opcion a enviar los informes!");
        return false;
    }
    $('input[id^="id_lapso"]').each(function(){
        if($(this).is(":checked")){
            flag=true;
            //console.log(this);
            return true;
        }
    });

    if(!flag){
        alert("¡Debe Seleccionar un momento!");
    }        
    return flag;

});