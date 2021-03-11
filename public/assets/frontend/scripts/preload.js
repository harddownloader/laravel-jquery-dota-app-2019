



var i = 3;
function loadTimeout(){
	console.log( i );

	i--;


	if(i==0){
		$('.preload').stop();
		$('.preload').fadeOut(1000);
		$("#scene").show();
	}
	
	else {
		setTimeout(loadTimeout, 1000);

		$('#timeout').html(i);
	}

 }


function simplePreload(){
	$(document).ready(function() {
	    console.log( "ready!" );

	    setTimeout(function(){
	    	$('.preload').stop();
	    	$('.preload').fadeOut(1000);
		},1000);
	});
}



function startPreloaderTimeout(){
	$('#timeout').html(i);
	$("#scene").hide();
	setTimeout(loadTimeout, 1000);
}




$(document).ready(function() {
	$(".hideonload").removeClass("hideonload");
  

	$('a').click( function(ev){

        var attr = $(this).attr('role');

        if (typeof attr === typeof undefined || attr === false) {
            var $self = $(this);

            if ($self.attr('href') == "" || $self.attr('href') == "#" || $self.attr('href') == null || $self.hasClass('nofadetrigger'))
                return;

            ev.preventDefault();


            $('body').stop();// нет при старте другиъ анимаций
            $('body').fadeOut(1000, function () {
                console.log($self.attr('href'));
                $(location).attr('href', $self.attr('href'));

            });

        }
});


$('.zoom-link').click( function(ev){


   ev.preventDefault();

   var $self=$(this);



	$('#back').stop(); // не нужен, так как это блок с фоном, анимация вызывается один раз, когда мы переходим к рулетке
    $( "#back" ).animate({
	    opacity: 0
	  }, 1000 );
    $('body').stop(); // не нужен, так как у body нет других анимаций, анимация вызывается один раз, когда мы переходим к рулетке
    $( "body" ).animate({
    	opacity:0,
	    zoom: 1.1
	  }, 2200 ,function(){

	  	console.log($self.attr('href'));
	  	$(location).attr('href', $self.attr('href'));

	  });

    

 });

});