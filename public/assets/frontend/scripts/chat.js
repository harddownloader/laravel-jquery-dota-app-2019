$("#menu-rsp").hover(
            function(){       
            if($("#toggle").prop("checked"))
                $('aside').addClass("rsp_open");                       
            },
            function(){            
                if($("#toggle").prop("checked"))
                $('aside').removeClass("rsp_open");                       
            }
        );
                $("#toggle").click(rsp_click);

                function rsp_click(){
                if(! $("#toggle").is(':checked') )
                    $('aside').removeClass("rsp_open");                              
                else{ //opened
                     $('aside').addClass("rsp_open");    
                     $('#chat-input').focus();
                     $('#scene').one("click",function(){
                        $("#toggle").prop("checked",false);
                        rsp_click();
                     });
                 }
                }




