<!DOCTYPE html>
<html lang="en" class="page-index">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/preload.css') }}"/>--}}
  <script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}" charset="utf-8"></script>
  <script src="{{ asset('assets/js/jquery.session.js') }}"></script>
  <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>


  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/main.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/media.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap-theme.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/animate.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/sparks.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/fonts.css') }}"/>


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/parallax.css') }}"/>

    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/windows.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/mymedia.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/custom-modal.css') }}">
    <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/toastr.js') }}"></script>





    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">

  <title>{{ $title }}</title>
</head>
<body class="my-font-regular">

    <div id="back"></div>
    {{--<div class="preload " id="preload-index">--}}
      {{--<div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"--}}
      {{--id="preload-text">--}}
        {{--<spam>{!! $lang['lobby']['preload_text'] !!}</spam>--}}
        {{--<br><br>--}}
        {{--<span id="timeout">5</span> <spam>{!! $lang['lobby']['remaining'] !!}</spam>--}}
      {{--</div>--}}
    {{--</div>--}}
    {{--<script src="{{ asset('assets/frontend/scripts/preload.js') }}"></script>--}}
    {{--<script>startPreloaderTimeout();</script>--}}

@if(Auth::check())
<div class="user-info hideonload" style="height: auto;">
        <a href="{{ route('profile') }}">
          <div class=" my-font-regular t-spacing" >
          <!--<div class="separator"></div>-->
              <div class="icon" style="background : url({{ $u->avatar }}); background-size : 100%; border-radius : 50%;"></div>
              <span>{{ $u->username }}</span>
              <small>LVL</small>
              <span id="info-level">{{ $u->lvl }}</span>
            <!--<div class="separator"></div>-->
            </div>
          </a>
        </div>
@endif

    <style>
        .costili-nav{
            z-index: 9999; width: 100px; position: absolute;
            right:70px; top: 13px;
        }
        .costili-nav button{
            position: absolute;
            right:0; top: 0;
            background: none;
            border: none;
            padding: 10px;
            height: 40px;
            text-shadow: none;
            color: #b0a9a8;
            box-shadow: none;
        }
        .costili-nav:hover{
            box-shadow: none !important;
            background: none !important;
        }
        .costili-nav button:hover{
            background: none;
            border: none;
            color: #fff;
        }
        .costili-nav:after{
            content: "";
            display: block;

            position: absolute;
            right: 0;
            top: 8px;
            height: 27px;
            width: 0px;
            border-right: 1px solid white;
        }
        .costili-nav .dropdown-menu{
            position: absolute;
            top: 30px;
            left: 9px;
            min-width: 82px;
            padding: 0;
            margin: 2px 0 0;
            font-size: 14px;
            background: none;
            box-shadow: none;
            border: none;
        }
        .costili-nav .dropdown-menu>li>a{
            color: #b0a9a8;
            font-size: 11px;
        }
        .costili-nav .dropdown-menu li a:hover{
            background: none;
        }
        .btn-default.active.focus, .btn-default.active:focus, .btn-default.active:hover, .btn-default:active.focus, .btn-default:active:focus, .btn-default:active:hover, .open>.dropdown-toggle.btn-default.focus, .open>.dropdown-toggle.btn-default:focus, .open>.dropdown-toggle.btn-default:hover{
            background: none;
            color: #b0a9a8;
            border: none;
        }
        .page-index #sound-btn{
            right: 200px !important;
        }
    </style>

<a id="menu-btn" target="#menu-window" class="hideonload window-link interface-btn" ></a>
    <div class="dropdown costili-nav hideonload interface-btn">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            @if(session()->get('language', 'en') == 'ru')
            RUS
            @else
            ENG
            @endif
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <li><a href="/lang/ru">RUS</a></li>
            <!-- <li><a href="/lang/ger">GER</a></li> -->
            <li><a href="/lang/en">ENG</a></li>
        </ul>
    </div>
<!--<input id="mute-btn" type="checkbox" onclick="togglePlay()">-->
<a id="sound-btn" class="hideonload interface-btn" ></a>

<div id="menu-window" class="my-window my-font-regular nav" blur="#scene,#menu-btn,.user-info,.interface-btn">
  <div class="btn-close"></div>
  @if(Auth::guest())<a href="{{ route('login') }}" id="steam-link" class="noselect"><img src="{{ asset('assets/frontend/images/steam.png') }}"></a>@endif


    <ul id="menu1">
        <li><a target="#about-window" class="window-link">ABOUT</a></li>
        <li><a data-toggle="modal" data-target="#myModalSupport">SUPPORT</a></li>
        <li><a href="{{ route('tutorial') }}">TUTORIAL</a></li>
        @if(Auth::check())
            <li><a href="{{ route('profile') }}">PROFILE</a></li>
            <li><a href="{{ route('logout') }}">LOG OUT</a></li>
        @endif
      </ul>
        <div id="social-icons" class="noselect">
            <a href="{{ $config->facebook }}"> <i class="fa fa-facebook" aria-hidden="true"></i></a>
            <a href="{{ $config->vk }}"><i class="fa fa-vk" aria-hidden="true"></i></a>
            <a href="{{ $config->youtube }}"><i class="fa fa-youtube" aria-hidden="true"></i></a>
            <a href="{{ $config->twitter }}"><i class="fa fa-twitter" aria-hidden="true"></i></a>
    </div>
</div>

<div class="modal custom fade my-font-regular t-spacing hcenter" id="myModalSupport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 class="modal-title my-font" id="myModalLabel">Support | Задайте свой вопрос поддержке</h3>
</div>
<div class="modal-body row">
<div class="col-xs-12">
<form id="support-form" style="z-index : 3000;">
<input type="text" id="sup-theme" placeholder="Тема" name="subject" required>
<input type="email" id="sup-email" placeholder="Email" name="email" required>
<textarea id="sup-message" cols="30" rows="6" placeholder="Сообщение" name="message" required></textarea>

<input type="submit" value="Отправить" style="display:block; cursor : pointer;">
</form>

<script type="text/javascript">
    $('#support-form').submit(function(e) {
        $.ajax({
            url : '/support',
            type : 'post',
            data : $(this).serialize(),
            success : function(data) {
                if(data.success) {
                    toastr.success(data.msg);
                    $('#sup-theme').val('');
                    $('#sup-email').val('');
                    $('#sup-message').val('');
                } else {
                    if(typeof data.msg != 'undefined') {
                        toastr.error('Ошибка при отправке данных!');
                    } else {
                        toastr.error(data.msg);
                    }
                }
            },
            error : function(err) {
                toastr.error('err');
                console.log(err.responseText);
            }
        });
        e.preventDefault();
        $('#myModalSupport').modal('hide');
    });
</script>
</div>
</div>
</div>
</div>
</div>

<div id="about-window" blur="#menu-window"  class="my-window my-font-regular">
  <div class="btn-close"></div>

  <div class="col-xs-12 col-xs-offset-0 col-md-4 col-md-offset-1 about-part">

      <div class="col-xs-12 ">
        <a href="{{ route('login') }}" class="about-link">
        <span>1. {!! $lang['lobby']['about_1'] !!}</span>

        <img src="{{ asset('assets/frontend/images/steam.png') }}" class="img-type1">
        </a>
      </div>


      <div class="col-xs-12 ">
      <a href="{{ route('withdraw') }}" class="about-link">
        <span>2. {!! $lang['lobby']['about_2'] !!}</span>
        <img src="{{ asset('assets/frontend/images/logo-block.png') }}" class="img-type1">
      </a>
      </div>
      <div class="col-xs-12 ">
      <a href="{{ route('deposit') }}" class="about-link">
        <span>3. {!! $lang['lobby']['about_3'] !!}</span>
        <img src="{{ asset('assets/frontend/images/pudge.png') }}" class="img-type1">
      </a>
      </div>


    </div>
    <div class="col-xs-12 col-xs-offset-0 col-md-4 col-md-offset-2  about-part">
      <div class="col-xs-12 ">
      <a href="{{ route('roulette') }}" class="about-link">
        <span>4. {!! $lang['lobby']['about_4'] !!}</span>
        <img src="{{ asset('assets/frontend/images/icons.png') }}" class="img-type1">
      </a>
      </div>
      <div class="col-xs-12 ">
      <a href="{{ route('home') }}" class="about-link">
        <span>5. {!! $lang['lobby']['about_5'] !!} </span>
        <img src="{{ asset('assets/frontend/images/min-logo.png') }}" class="img-type1">
      </a>
      </div>
      <div class="col-xs-12 ">
      <a href="{{ route('profile') }}" class="about-link">
        <span>6. {!! $lang['lobby']['about_6'] !!}</span>
        </a>

      </div>

  </div>

</div>


    <div blur="#menu-window" class="agreement my-window my-font-regular">

        <div class="col-xs-8 col-xs-offset-2">

            <h4 style="color:#fff;">{!! $lang['lobby']['license_title'] !!}</h4>

            {!! $lang['lobby']['license'] !!}

            <div class="checkbox">
                <label>
                    <input class="check-agreement" type="checkbox"> {!! $lang['lobby']['license_accept'] !!}
                </label>
            </div>

        </div>

    </div>


<div id="sparks-container"></div>
<!--
<div id="sidebar">
    <input type="checkbox" id="nav-toggle"  hidden>
    <nav class="nav">


    <label for="nav-toggle" class="nav-toggle" onclick><i class="fa fa-bars" aria-hidden="true"></i></label>
	<a href="#" id="steam-link"><img src="images/steam.png"></a>


	  <ul id="menu1">
        <li><a target="#about-window" blur="#menu1,#steam-link,#social-icons" class="window-link">ABOUT</a></li>
        <li><a href="#2">GAMES</a></li>
        <li><a href="#3">RULES</a></li>
        <li><a href="#4">CONNECT</a></li>

      </ul>
				<div id="social-icons">
		<a href="http://www.facebook.com/YOUR_FB_IDENTIFIER"><img src="images/icons/fb.png" width="48" height="48" alt="Facebook" /></a>
		<a href="http://store.steampowered.com"><img src="images/icons/steam.png" width="48" height="48" alt="Steam" /></a>
		<a href="https://twitter.com/YOUR_USER_NAME"><img src="images/icons/twitter.png" width="48" height="48" alt="Twitter" /></a>
	</div>
    </nav>
</div>
-->
<div id="scene" class="hideonload">

                <div id="wrapper" class="layer noblur" data-depth="0.10" style=" z-index:3001 !important; width:100%">

                  <div class="container">

                    <div id="main_logo">
                      <div class="row">
                        <div class="col-md-4 col-sm-3 col-xs-2"></div>
                        <div class="col-md-4 col-sm-6 col-xs-8 " style="    height: 150px;">
                          <img src="{{ asset('assets/frontend/images/index_logo.png') }}"  class="layer" data-depth="0.05" alt="">

                        </div>
                        <div class="col-md-4 col-sm-3 col-xs-2"></div>
                      </div>
                    </div>


                    <div class="row">
                      <div class="col-md-3 col-sm-3 col-xs-3"></div>
                      <div class="col-md-6 col-sm-6 col-xs-6" style="margin-left: -10px;">
                        <div id="main_ch_g" class=" t-spacing">



                            <div id="main_ch_g_circle">
                              <div id="main_ch_g_roulette" class="main_ch_g_ch"><a href="{{ route('roulette') }}" class="zoom-link nofadetrigger click_and_start_trigger">{!! $lang['lobby']['roulette'] !!}</a></div>
                              <div id="main_ch_g_blackjack" class="main_ch_g_ch click_and_start_trigger"><a href="{{ route('slot.machine') }}" class="zoom-link nofadetrigger click_and_start_trigger">{!! $lang['lobby']['bandit'] !!}</a></div>
                              <div class="game_icon" id="roulette_icon"><img src="{{ asset('assets/frontend/images/roulette_ico.png') }}" alt=""></div>
                              <div class="game_icon" id="jackpot_icon"><img src="{{ asset('assets/frontend/images/blackjack_ico.png') }}" alt=""></div>
                              <div class="game_icon" id="blackjack_icon"><img src="{{ asset('assets/frontend/images/jeckpot_ico.png') }}" alt=""></div>
                            </div>
                            <div id="main_ch_g_jackpot" class="main_ch_g_ch click_and_start_trigger"><a href="{{ route('poker') }}" class="zoom-link nofadetrigger click_and_start_trigger">{!! $lang['lobby']['poker'] !!}</a></div>
                        </div>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3"></div>
                    </div>

                    <div class="row">
                      <div class="col-md-3 col-sm-3"></div>

                      <div class="col-md-6 col-sm-6">
                        <div class="">
                          <p id="click_and_start">{!! $lang['lobby']['cas'] !!}</p>
                        </div>
                      </div>

                      <div class="col-md-3 col-sm-3"></div>
                    </div>

                  </div>
                </div>

</div>

				<audio autoplay loop id="myAudio" preload="auto">
				  <source src="{{ asset('assets/frontend/sounds/volcano.mp3') }}" type="audio/mpeg">
				{!! $lang['lobby']['audio_error'] !!}
				</audio>




				<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js'></script>
				<script src="{{ asset('assets/frontend/scripts/sparks.js') }}"></script>
				<script src="{{ asset('assets/frontend/scripts/jquery.parallax.js') }}"></script>
        <script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}" charset="utf-8"></script>
				<script src="{{ asset('assets/frontend/scripts/main.js') }}" charset="utf-8"></script>

        <script src="{{ asset('assets/frontend/scripts/windows.js') }}" charset="utf-8"></script>


        <script src="{{ asset('assets/frontend/scripts/audio.js') }}" charset="utf-8"></script>


    <style>
        .agreement{
            background-color: rgba(0, 0, 0, 0.9);
        }
        .check-agreement{
            display: block !important;
        }
    </style>

				<script>
					$('#scene').parallax();

					$(document).ready(function () {

					    setTimeout(function () {

                            $('.agreement.my-window').fadeIn('1000');

                        }, 4000);

					    $('.check-agreement').click(function () {
                            $('.agreement.my-window').fadeOut('1000');
                        });

                    });

				</script>



<div style="display: none;">
{{ $language = session()->get('language', 'en') }}
@if($language == 'en') 
{{ $message = $config->alert_message_en }}
@else
{{ $message = $config->alert_message_ru }}
@endif  
</div>

@if($config->alert_active)
<script>
  $(document).ready(function() {
    var type = '{{ $config->alert_type }}';
    switch(type)
    {
      case 'success' :
        toastr.success('{{ $message }}');
        break;
      case 'error' :
        toastr.error('{{ $message }}');
        break;
      case 'warning' :
        toastr.warning('{{ $message }}');
        break;
      case 'info' :
        toastr.info('{{ $message }}');
        break;
    }
  });
</script>
@endif
</body>
</html>
