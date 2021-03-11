$(document).ready(function(){
  window.current = 1;
  setActiveBtn(current);
  window.pause = false;
  window.sliderTimer = setInterval( go, 5000);

  $( "#slider_container" ).hover(
    function() {
      pause = true;
      //console.log("pause");
    }, function() {
      pause = false;
      //console.log("un-pause");
    }
  );
});

/* PARAMS: next index (optional), current index (optional) */
function go(){
  if(pause){
    restartTimer();
    return false;
  }

  if(arguments[1]){
    restartTimer();
  }

  var picWidth = 300,
      max = 5,
      bumpit = "",
      /* HANDLE THE PREV() INDEXING BELOW 1 */
      pos = (arguments[0] === undefined) ? ++current : ( arguments[0] === 0 ? max : arguments[0]);
  current = (arguments[1] === undefined) ? current : ( arguments[0] === 0 ? max : arguments[0]);

  setActiveBtn(current);

  if( pos <= max ){
    bumpit = ( pos - 1 ) * picWidth;
    bumpit = "-" + bumpit + "px";
    $("#slider ul").animate( {left: bumpit }, 1000 );
  } else {
    current = 1;
    go(1);
  }
}

function restartTimer() {
  clearInterval(sliderTimer);
  sliderTimer = setInterval( go, 5000);
}

function setActiveBtn(index){
  $("#slider_btns li").removeClass("active").not( $("#slider_btns li").eq(index - 1).addClass("active") );
}

function next() {
  var nextIndex = current + 1;
  go(nextIndex, nextIndex);
}

function prev() {
  var prevIndex = current - 1;
  go(prevIndex, prevIndex);
}