
$( document ).ready(function() {

   maybe();
   
});

function firstpromise(){
    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var baserurl=re.exec(window.location.href);
    var courseid = $("#id_courseid").val();
    
   return $.ajax({
            url : baserurl[0]+'/map_grade_ajax.php',
            data : { id : courseid , t : 2},
            type : 'POST',
            dataType : 'json',
            success : function(json) {
                //console.log(json);
                if(json!=false){
                    for(var element in json){
                        element=json[element];
                        
                        var id_r=element['lp']
                        var n=element['ev'];
                        var o="-"+id_r+"-"+n;

                        var html= rhtml(id_r,n);

                        $("#lp-"+id_r).append(html);
                    }
                }
            },
            error : function(xhr, status) {
                alert('2 Disculpe, ocurrio un problema');
            },
            complete : function(xhr, status) {
                //alert('Petición realizada');
            }
        });   

}


$("input[id^='add-']").click(function() {

    var re = /add-([0-9]*)/ig;
    var id_r=re.exec($(this).attr('id'))[1];
 
    var last_id=0;
    $( "div[id^='ev-"+id_r+"']").each(function( index ) {
        var re = /ev-[0-9]-([0-9]*)/ig;
        var id=re.exec($(this).attr('id'));
        last_id=id[1];
    });
    if(id_r==4 && last_id==1){
        alert("Solo se permite una evaluacion para la revision.");
        return;
    }
    var sel='lp-'+id_r+'-'+last_id;

    if($("select[name='"+sel+"']").children("option:selected").val()=='0'){
        alert("Seleccione una evaluacion antes de continuar agregando evaluaciones.");
        return;
    }
    
    var n=parseInt(last_id)+1;
    var o="-"+id_r+"-"+n;
    
    var html= rhtml(id_r,n);

    $("#lp-"+id_r).append(html);
    var id_s="select[name^='lp"+o+"'], select[name^='cd"+o+"']";
    
    items_request_new(id_s,o);
    maybe();

});

function rhtml(id_r,n){

    var o="-"+id_r+"-"+n;
    var html= '<div class="col-md-9 form-inline felement" data-fieldtype="group" id="ev'+o+'">   '+  
    '<div class="form-group  fitem  "> '+  
    '   <label for="lp'+o+'">Eval '+n+'</label> '+  
    '<select name ="lp'+o+'" class="custom-select"> '+  
    '    <option value = "0" selected>Seleccione el item</option>'+  
    '</select> '+
    '</div> '+
    '<div class="form-group  fitem  "> '+
    '    <label for="cd'+o+'">112 </label> '+
    '<select name ="cd'+o+'" class="custom-select">'+
    '    <option value = "0" selected>Seleccione el item</option>'+
    '</select>'+
    '</div>'+
    '<div class="col-md-1"> </label>'+
    '</div>'+
    '<div class="col-md-2">'+
    //'    <input type="number" class="form-control " name="pc'+o+'" id="pc'+o+'" value=""  min="0" max="100" >'+
    '</div>'+
    '</div>';

    $("#lp-"+id_r).append(html);
}

function   items_request(selectors){   

    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var id_r=re.exec(window.location.href);
    var courseid=$("#id_courseid").val();

    $.ajax({
        url : id_r[0]+'/map_grade_ajax.php',
        data : { id : courseid , t : 0},
        type : 'POST',
        dataType : 'json',
        success : function(json) {
            
            $( selectors ).each(function( index ) {
                var select=this;
                for(var element in json){
                    element=json[element]
                    $(this).append('<option value = "'+element.id+'">'+element.itemname+'</option>');
                };
            });
          
        },
        error : function(xhr, status) {
            alert('0 Disculpe, ocurrio un problema');
        },
        complete : function(xhr, status) {
            //alert('Petición realizada');
        }
    });
};

 function  items_request_new(selectors,o){   

    var re = /(.)*(?=\/.*\.php?.*)/ig;
    var baserurl=re.exec(window.location.href);
    var courseid=$("#id_courseid").val();
    
    return $.ajax({
        url : baserurl[0]+'/map_grade_ajax.php',
        data : { id : courseid,t:0 },
        type : 'POST',
        dataType : 'json',
        success : function(json) {
            
            $( selectors ).each(function( index ) {
                var select=this;
                for(var element in json){
                    element=json[element]
                     $(this).append('<option value = "'+element.id+'">'+element.itemname+'</option>');
                };

            });

            $("select[name^='lp-'], select[name^='cd-']").each(function( index ) {
                var valueSelected = $(this).children("option:selected").val();

                if(($(this).attr('name')!="lp"+o && $(this).attr('name')!="cd"+o) && valueSelected!=0){

                    $(selectors).each(function(index){

                        $(this).children("option[value='"+valueSelected+"']").remove();

                    });
                }
            });
            
        },
        error : function(xhr, status) {
            alert('1 Disculpe, ocurrio un problema');
        },
        complete : function(xhr, status) {
            //alert('Petición realizada');
            
        }
    });
   
};

function maybe(){
   var previous;

    $("div[id^='ev-']").on('click',"select[name^='lp-'], select[name^='cd-']",function(){
        
        previous=undefined;
        previous = $("option:selected", this);

    }).on('change',"select[name^='lp-'], select[name^='cd-']", function (e) {
        
        var valueSelected = this.value;
        var name=this.name;
       

        $("select[name^='lp-'], select[name^='cd-']").each(function( index ) {
           
            if($(this).attr('name')!=name){
                previous.removeAttr("selected");
                if(previous[0].value!=0 ){        
                    $(this).append(previous[0].outerHTML);  
                }

                if(valueSelected!=0){
                    $(this).children("option[value='"+valueSelected+"']").remove();
                }  

            }  
        });
        
    });

}

$('form[id^="course_"]').submit(function(event) {

    submit_id=event.originalEvent.submitter.id
    
    flag=true;

    if(submit_id=='id_submit'){

          /*  $('input[name^="pc-').each(function(){
            
                if(!(this.value)){
                    alert("¡Existen evaluaciones sin porcentaje! \n Asigne porcentaje a las evaluaciones y vuelva intentar.");
                    flag= false;
                    return flag;
                }

            });

            if(!flag){
                return false;
            }
       
            for(let i=1; i<=4 ;i++){
                
                sum=0;
                
                $('fieldset[id="lp-'+i+'"] > div > div > input[name^="pc-"]').each(function(){
                        sum+=parseInt(this.value);
                });
            
                if(sum > 0 && sum!=100 ){
                    alert("Los porcentajes de las evaluaciones deben sumar 100(%). \n Revise los porcentages y vuelva a intentarlo.");
                    return false;
                }

            } */  
         
           
    }
    else if(submit_id=='id_reset'){

        var r = confirm("¿Esta seguro de reestablecer la asignacion de evaluaciones? \n\n Esta accion no puede deshacerse una ves hecha.");
        
        if (r == false) {
          return false;
        }   

    }
    else if(submit_id=='id_cancel'){
        
        var r = confirm("¿Esta seguro de salir de esta pagina? \n\n Las configuraciones no guadadas se perderan.");
        
        if (r == false) {
          return false;
        }   

    }
    
});



