

function playSwitch(){
var audio = new Audio('assets/frontend/sounds/switch.mp3');
	audio.currentTime = 0;
	audio.play();
	console.log("play");
}

function playHorn(){
var audio = new Audio('assets/frontend/sounds/deep-horn.mp3');
	audio.currentTime = 0;
	audio.play();
	console.log("play");
}
function playMenu1(){
var audio = new Audio('assets/frontend/sounds/menu-hover.mp3');
	audio.currentTime = 0;
	audio.play();
	console.log("play");
}
var counter =1;
$(document).ready(function() { // вся мaгия пoсле зaгрузки стрaницы



/////////////
  $("#menu1 li").mouseenter(function () {
    playMenu1();
  });

  $("#main_ch_g div a").click(function () {
    playHorn();
  });
   $("#menu1 li").click(function () {
   playHorn();
  });

///////////

  $("#main_ch_g_roulette").mouseenter(function () {
    $("#roulette_icon").addClass('active');
	playSwitch();
    $("#main_ch_g_circle").addClass('c_active');
  });
  $("#main_ch_g_roulette").mouseleave(function () {
    $("#roulette_icon").removeClass('active');
    $("#main_ch_g_circle").removeClass('c_active');

  });

  $("#main_ch_g_blackjack").mouseenter(function () {
    $("#blackjack_icon").addClass('active');
	playSwitch();
    $("#main_ch_g_circle").addClass('c_active');
  });
  $("#main_ch_g_blackjack").mouseleave(function () {
    $("#blackjack_icon").removeClass('active');
    $("#main_ch_g_circle").removeClass('c_active');

  });

  $("#main_ch_g_jackpot").mouseenter(function () {
    $("#jackpot_icon").addClass('active');
	playSwitch();
    $("#main_ch_g_circle").addClass('c_active');
  });
  $("#main_ch_g_jackpot").mouseleave(function () {
    $("#jackpot_icon").removeClass('active');
    $("#main_ch_g_circle").removeClass('c_active');

  });

  var tag;
  $(".nav-toggle").click(function () {

    $(this).children().remove();

	console.log(counter+" "+ $(".noblur").length );
		/*
	if(counter==1){

		if($("#counter1").length ==0){

		}
	}*/
     if(counter % 2 ){
		blurMenu();
    }
	else{
		noblurMenu();
    }

    $(this).append(tag);
    counter++;
  });


function blurMenu(){
tag = '<i class="fa fa-times" aria-hidden="true"></i>';

      $("#wrapper").addClass('blur');
	  	  setTimeout(function(){
		$("#wrapper").removeClass('noblur');
	  },400);
}
function noblurMenu(){
 tag ='<i class="fa fa-bars" aria-hidden="true"></i>';

	  $("#wrapper").addClass('noblur');
	  setTimeout(function(){
	    $("#wrapper").removeClass('blur');
		 $("#wrapper").removeClass('noblur');
	  },400);

}

});
