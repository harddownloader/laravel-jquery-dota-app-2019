var myAudio = document.getElementById("myAudio");
var isPlaying = false;

function togglePlay() {
  if (isPlaying) {
    myAudio.pause();
    
  } else {
    myAudio.play();
  }
  setCookie('audioMute', isPlaying);
};
myAudio.onplaying = function() {
  isPlaying = true;
};
myAudio.onpause = function() {
  isPlaying = false;
};




function setCookie(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
      x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
      y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
      x=x.replace(/^\s+|\s+$/g,"");
      if (x==c_name)
        {
        return unescape(y);
        }
      }
}

var song = document.getElementsByTagName('audio')[0];
var played = false;
var tillPlayed = getCookie('timePlayed');
function updateAudio()
{
    if(!played){
        if(tillPlayed){
        song.currentTime = tillPlayed;
        song.play();
        played = true;
        }
        else {
                song.play();
                played = true;
        }
    }

    else {
    setCookie('timePlayed', song.currentTime);
    }
}

updateAudio();


window.onbeforeunload = function (e) {
	updateAudio();
}

$(document).ready(function(){
var res = getCookie('audioMute');
  if(res == "true"){    
    myAudio.pause();
    $("#mute-btn").prop('checked', true);
}
});