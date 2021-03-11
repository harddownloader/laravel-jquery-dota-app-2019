var defaulttext = $('.default-text').text();

$('.selectDefault').text(defaulttext);

$('.selectBox').on('change',function(){
   var defaulttext2 = $('.selectBox').find(":selected").text(); 
    $('.selectDefault').text(defaulttext2);
});