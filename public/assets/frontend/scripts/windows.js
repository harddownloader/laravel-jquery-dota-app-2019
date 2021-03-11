


//var $tmpblur;



/*
$('#about-window').hide();
$('#menu-window').hide();
*/
$('.my-window').hide();

$('#nav-toggle').first().prop('checked', false);




var $blockclick = false;



$('.btn-close').click(function(){

	if($blockclick == true){
   		console.log("blocked close");
   		return;
   }

	var $self=$(this);
	console.log("btn-close");
	$blockclick = true;
	

	$self.css("pointer-events","none");
	//block brn-close

	
	$self.parent().stop;	
	$self.parent().fadeOut(1000);	

	var $blur = $self.parent().attr("blur");

	
	$( $blur ).addClass("blur-transition");

	setTimeout(function(){
		$( $blur ).removeClass("blur-window");
		$( $blur ).removeClass("blur-transition");


			//console.log('.window-link[target="#'+$self.parent().attr('id')+'"]');
			//unblock link
		$('.window-link[target="#'+$self.parent().attr('id')+'"]').css("pointer-events","auto");


		$self.css("pointer-events","auto");
		//unblock brn-close

		$blockclick = false;
	},1000);
	//$($tmpblur).removeClass("blur-window");
});


$('.window-link').click(function(ev){

   ev.preventDefault();

   if($blockclick == true){
   	console.log("blocked link");
   	return;
   }
   $blockclick = true;


	var $self=$(this);
	$self.css("pointer-events","none");
	//block link



	
	$($self.attr("target")).stop();
	$($self.attr("target")).fadeIn(function(){
		$blockclick = false;
	});

	$($self.attr("target")).attr("display","block");


	//$('.nav-toggle').fadeOut();
	//$tmpblur = $self.attr("blur");



	var $blur =$($self.attr("target")).attr("blur");	

	$( $blur ).addClass("blur-window");

});
