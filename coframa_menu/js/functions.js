$( document ).ready(function() {

    
});


$( 'input[name="gsearch"]').keyup(function() {
   
    load_form(this.value);
});

function load_form(key){
    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var baserurl=re.exec(window.location.href);

   return $.ajax({
            url : baserurl[0]+'/meta_ajax.php',
            data : { payload: key},
            type : 'POST',
            dataType : 'json',
            success : function(json) {
                $('.rmv').remove();
                if(json!=false){
                    var html="";
                    var i=1;
                    
                    for(var element in json){
                        element=json[element];

                        html+=  '<div  name="search_'+element.userid+'" class="form-group row  fitem rmv" > '+
                                    '<label class="col-md-3"></label>'+
                                    '<label style="background-color: lightblue" " class="col-md-4" >'+element.data +' - '+element.firstname +', '+element.lastname+'</label> '+
                                    '<input type="hidden" name="name_'+element.userid+'" value="'+element.data +' - '+element.firstname +', '+element.lastname+'"> '+
                                    '<input type="hidden" name="userid_'+element.userid+'" value="'+element.userid+'"> '+
                                '</div>'; 
                                i++;
                    }

                    $('fieldset[id="id_Informe"]').append(html);
                    click_form();
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

function click_form(){
    $('div[name^="search_"]').click(function(){
        $('input[name="meta_value"]').val($(this).children('input[name^="name_"]').val());
        $('input[name="userid"]').val($(this).children('input[name^="userid_"]').val());
        $('input[name="gsearch"]').val('');
        $('.rmv').remove();
    });
}



$('form[id^="mform1_"]').submit(function(event) {

   console.log(event);
    if($('input[name="meta_value"]').val()==""){
        alert("¡Debe ingresar el nuevo valor para proceder!");
        return false;
    }
    
    if (confirm('¿Esta Seguro de Actualizar esta informacion?')) {
       
        return true;
    } else {
        window.history.back();
        return false;
    }
  
});
