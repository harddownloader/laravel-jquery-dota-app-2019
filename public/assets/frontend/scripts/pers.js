

var fps = 30 , // frame rate per second
    totalFrames = 88 , // your Sprite animation frames counts
    dur = (1/fps)*(totalFrames-1); // tween duration
var SpriteWidth = 84480/88*totalFrames ; // width of your Sprite image


TweenMax.to('.character',dur,{repeat:-1,backgroundPosition:-SpriteWidth+'px',ease:SteppedEase.config(totalFrames)});

var fps1 = 25 , // frame rate per second
    totalFrames1 = 25 , // your Sprite animation frames counts
    dur1 = (1/fps1)*(totalFrames1-1); // tween duration
var SpriteWidth1 = 5304/25*totalFrames1 ; // width of your Sprite image


function myFunction(){
	$(".anim-ball").hide();
}


TweenMax.to('.anim-ball',dur1,{onComplete:myFunction,repeat:3,backgroundPosition:-SpriteWidth1+'px',ease:SteppedEase.config(totalFrames1)});



var fps2 = 30 , // frame rate per second
    totalFrames2 = 88 , // your Sprite animation frames counts
    dur2 = (1/fps2)*(totalFrames2-1); // tween duration
var SpriteWidth2 = 84568/88*totalFrames2 ; // width of your Sprite image


TweenMax.to('.character2',dur2,{repeat:-1,backgroundPosition:-SpriteWidth2+'px',ease:SteppedEase.config(totalFrames2)});
