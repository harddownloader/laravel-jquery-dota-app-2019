


var enablekeys = false; // для проверки

var menu = false;//для show menu


$(document).ready(function(){

// setTimeout(function() {
//     shuffle();
// }, 3000);

// menu sound
  $(".bandit-menu a").mouseenter(function () {
                    playMenu1();
                    });

// поворот указателя
  // $(".select-rotator").hover(
  //   function(){
  //     var $deg = $(this).attr("select-rotator");
  //     rotateselector($deg);
  //
  //   },
  //   function(){
  //       rotateselector(-90);
  //   }
  //   );

//Для проверки
  if(enablekeys)
  $("html").keypress(function( event ) {
      if(event.which == 109){//M
       bandit_showmenu(!menu);
      }

      else if(event.which == 48)//0
        showgame(0);
      else if(event.which == 49)//1
        showgame(1);
      else if(event.which == 50)//2
        showgame(2);
      else if(event.which == 51)//3
        showgame(3);

      else if(event.which == 115)//s
        shuffle();
      else if(event.which == 100)//d
        scrollbandit();
      else if(event.which == 114)//r
        rotateselector(90);

      else if(event.which == 101)//e
        rotateElement(1,1,1);

      //alert(event.which);

    });

});


    function showgame(game_n){
      if(game_n==0){
        $(".bandit-container").removeClass("show-game1");
        $(".bandit-container").removeClass("show-game2");
        $(".bandit-container").removeClass("show-game3");
      }

      else if(game_n==1){
        $(".bandit-container").addClass("show-game1");
        $(".bandit-container").removeClass("show-game2");
        $(".bandit-container").removeClass("show-game3");
      }
      else if(game_n==2){
        $(".bandit-container").addClass("show-game2");
        $(".bandit-container").removeClass("show-game1");
        $(".bandit-container").removeClass("show-game3");
      }
      else if(game_n==3){
        $(".bandit-container").addClass("show-game3");
        $(".bandit-container").removeClass("show-game1");
        $(".bandit-container").removeClass("show-game2");
      }
    }

    function bandit_showmenu(val){
      if(val)
        $(".bandit-container").addClass("show-menu");
      else
        $(".bandit-container").removeClass("show-menu");

        menu = val;

    }



        function shuffle() {
        //   $(".bandit-container").addClass("bandit-shuffle");

              swap(5,3,4,1,200);
              swap(1,3,4,2,500);

              swap(1,1,4,3,700);
              swap(3,2,5,2,700);
              swap(2,2,1,2,700);

              swap(2,3,5,1,1100);
              swap(2,1,3,3,1100);


              setTimeout(function(){
                // $(".bandit-container").removeClass("bandit-shuffle");

              },4500);
        }

        function swap(col1,row1,col2,row2,delay){
            setTimeout(function(){
                moveElement(col1,row1,col2,row2);
                moveElement(col2,row2,col1,row1);

            },delay);
        }



                    function moveElement(col,row,t_col,t_row){
                        col--;
                        row--;
                        t_col--;
                        t_row--;

                        var dir_col = t_col-col;
                        var dir_row = t_row-row;

                        var col_text = "";
                        if(dir_col == 0)
                            col_text = "0%";
                        else if(Math.abs(dir_col) == 1)
                            col_text = "101%";
                        else if(Math.abs(dir_col) == 2)
                            col_text = "202%";
                        else if(Math.abs(dir_col) == 3)
                            col_text = "302%";
                        else if(Math.abs(dir_col) == 4)
                            col_text = "407%";

                        if(dir_col<0)
                            col_text = "-"+col_text;


                        var row_text  =""+(Math.abs(dir_row)*100)+"%";
                        if(dir_row<0)
                            row_text = "-"+row_text;

                        var str = "translate("+col_text+", "+row_text+")";


                    //    console.log(str);

                        var el = getElement(col,row);
                        var t_el = getElement(t_col,t_row);
                        var css1 =" "+getElement(col,row).css("background-image").toString();
                        var css2 =" "+ getElement(t_col,t_row).css("background-image").toString();

                        el.css({
                            'transition': 'transform 0.5s',
                            'transform':str}).on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
                                el.css({
                                    'transition': 'transform 0.5s',
                                    'transform' : "translate(0%,0%)"
                                });
                                  el.css({"background-image":css2});

                            });
                     }
                     function getElement(col,row) {
                          return $(".slot:eq("+col+") .slot-element:eq("+row+")")
                      }



                      //SCROLL///////////////////////////////////////


                    function scrollbandit(){
                        for (var i = 0; i < 5; i++) {
                            sc(i);
                        }
                    }



                    function sc(n){
                     setTimeout(function(){
                         scrollslot(n);
                    },0 + n*500);

                    }

                    function scrollslot(n){
                        console.log("val: "+n);

                       // $(".bandit-block .slot:eq("+n+")").scrollTop(10000);

                        $el = $(".bandit-block .slot:eq("+n+")");
                        $el.animate({
                                        scrollTop: $el.offset().top+3000
                                    }, 6000);


                    }


                    //ROTATESELECTOR///////////////////////////////////////

                    function rotateselector(deg){
                      deg = -deg;
                      $el = $(".selector");
                      $el.css({
                            'transition': 'transform 3s',
                            'transform':"rotateZ("+deg+"deg)"
                            });


                    }



                    function rotateElement($el,$img,$classname){

                    var $time = 1;
                    $el.css('cursor', 'no-drop');
                    // $(".bandit-container").addClass("bandit-shuffle");
                    deg = 90;
                    //$el = $(".selector");
                    $el.css({
                    'transition': 'transform '+($time/2)+'s',
                    'transform':"rotateY("+deg+"deg)"
                    }).on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){

                    $el.css({
                    'transition': 'transform 0s',
                    'background-image' : "url("+$img+")"

                    });

                    $el.css({
                    'transition': 'transform '+($time/2)+'s',
                    'transform':"rotateY(0deg)"
                    })
                    // .on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
                    // $(".bandit-container").removeClass("bandit-shuffle");
                    // });
                    });


                    setTimeout(function() {
                        $el.addClass($classname);
                        $el.css('cursor', 'pointer');
                    }, 4400);

                    }
