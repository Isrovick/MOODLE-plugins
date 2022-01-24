$( document ).ready(function() {

    
});

$( 'input[name="gsearch"]').keyup(function() {
   
    load_form(this.value);
});

function load_form(key){
    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var baserurl=re.exec(window.location.href);

   return $.ajax({
            url : baserurl[0]+'/map_promo_ajax.php',
            data : { payload: key},
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
                alert('4 Disculpe, ocurrio un problema');
            },
            complete : function(xhr, status) {
                //alert('Petición realizada');
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

    var userid=$('input[name^="userid_"]').length;
    
    if( !userid){      
            alert("¡Debe ingresar el/los alumnos a desmatricular!");
            return false;  
    }
    
  return true;
});
