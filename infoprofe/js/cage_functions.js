$('fieldset.containsadvancedelements').click(function(){
console.log(this);
    var content= $(this).children('div[class="content"]');

    console.log(content);

     if (content.css("display") === "block") {
        content.css("display","none");
      } else {
        content.css("display","block");
      }
})