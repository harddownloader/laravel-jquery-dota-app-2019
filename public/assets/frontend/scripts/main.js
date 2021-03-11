//
// var soundStatus = localStorage.getItem('dotaregal_sound');
// if(soundStatus == null) localStorage.setItem('dotaregal_sound', true);
// if(!soundStatus) {
//     $('#sound-btn').stop();
//     $('#sound-btn').addClass('mute');
//     $muted = false;
// }


var $tracks = [];




var $muted = localStorage.getItem('dotaregal_sound');
if($muted == null) {
    $muted = false;
    localStorage.setItem('dotaregal_sound', false);
}

if(typeof $muted == 'string') {
    if($muted == 'false') $muted = false;
    if($muted == 'true') $muted = true;
}

if(!$muted) $('#sound-btn').addClass('mute');

if($muted) mute(false); else mute(true);

$("#sound-btn").click(function(){
  var $sound= $('#myAudio')[0];

  if($muted){
    mute(false);
  }
  else mute(true);


});




function mute(p){
  if(p){
      for(var i=0; i<$tracks.length; i++){
        $tracks[i].volume = 0;
      }
      $('#myAudio')[0].volume = 0;
      $('#sound-btn').stop();
      $('#sound-btn').addClass('mute');
      localStorage.setItem('dotaregal_sound', false);
    }
  else {
    for(var i=0; i<$tracks.length; i++){
        $tracks[i].volume = 1;
    }
    $('#myAudio')[0].volume = 1;
    $('#sound-btn').stop();
    $('#sound-btn').removeClass('mute');
    localStorage.setItem('dotaregal_sound', true);
  }
  $muted = p;
}


function addSound(audio){
    if($muted) audio.volume = 0;
   $tracks.push(audio);
}
function playSwitch(){
  playSound('assets/frontend/sounds/switch.mp3');
}

function playHorn(){
  playSound('assets/frontend/sounds/deep-horn.mp3');
}
function playMenu1(){
  playSound('assets/frontend/sounds/menu-hover.mp3');
}





function playSound(path){
  var audio = new Audio(path);
  audio.play();
  addSound(audio);

}

var counter =1;
$(document).ready(function() { // вся мaгия пoсле зaгрузки стрaницы

$tracks.push($('#myAudio')[0]);
///////////////////

        $("#click_and_start").hide();

$(".click_and_start_trigger").hover(
      function () {
        $("#click_and_start").stop();
        $("#click_and_start").fadeIn();
      },
      function () {
        $("#click_and_start").stop();
        $("#click_and_start").fadeOut();
      }
      );



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
	  	$("#wrapper").stop();
      $("#wrapper").addClass('blur');
	  	  setTimeout(function(){
		$("#wrapper").removeClass('noblur');
	  },400);
}
function noblurMenu(){
 tag ='<i class="fa fa-bars" aria-hidden="true"></i>';
  $("#wrapper").stop();
	  $("#wrapper").addClass('noblur');
	  setTimeout(function(){
	    $("#wrapper").removeClass('blur');
		 $("#wrapper").removeClass('noblur');
	  },400);

}

});
