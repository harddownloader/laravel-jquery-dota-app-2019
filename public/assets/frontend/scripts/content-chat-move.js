
var $windowMoveToMove = 904;
var $moveOn = false;
checkMoveContent();
function checkMoveContent(){
if($(window).width() > $windowMoveToMove){
        $moveOn = true;
    }
    else{
        $moveOn = false;
        $('.bandit-container').css({"right":"0px"});   
    }
}



        $("#menu-rsp").hover(
            function(){       
                if($moveOn)
            if($("#toggle").prop("checked"))
                $('.bandit-container').css({"right":"220px"});                       
            },
            function(){    
            if($moveOn)        
                if($("#toggle").prop("checked"))
                $('.bandit-container').css({"right":"220px"});                      
            }
        );

        $("#toggle").click(moveClick);

        function moveClick(){
            if($moveOn)
                if(! $("#toggle").is(':checked') )
                   $('.bandit-container').css({"right":"0px"});                              
                else{
                    $('.bandit-container').css({"right":"220px"});    
                    $('#scene').one("click",function(){
                        //$("#toggle").prop("checked",false);
                        console.log("scene move");
                     $('.bandit-container').css({"right":"0px"});   

                     });
                }
        }

$(window).resize(function(){
    checkMoveContent();
});
