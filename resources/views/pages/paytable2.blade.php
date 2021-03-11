<!DOCTYPE html>
<html lang="en" class="bandit-html">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title }} - {{ $config->sitename }}</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/preload.css') }}"/>


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/fonts.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bandit.css') }}">
	<script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}" charset="utf-8"></script>
	<script src="{{ asset('assets/js/jquery.session.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}" charset="utf-8"></script>


    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/fog.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/bandit-lines.css') }}">

	@if(Auth::check())
		<script type="text/javascript">
			const USER_ID = parseFloat('{{ $u->id }}');
			const USER_USERNAME = '{{ $u->username }}';
			const USER_STEAMID64 = '{{ $u->steamid64 }}';
			const USER_AVATAR = '{{ $u->avatar }}';
		</script>
	@endif

	@if(Auth::check())
		<script src="{{ asset('assets/js/online.js') }}"></script>
	@endif
	<script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
</head>
<body>


<div class="preload " id="preload-simple">
  <div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"
  id="preload-text">
  <img src="{{ asset('assets/frontend/scripts/preload.js') }}"
  class="img-responsove vcenter" style="width: 100px;">
  </div>
</div>
<script src="{{ asset('assets/frontend/scripts/preload.js') }}"></script>
<script>simplePreload();</script>
<canvas id="myCanvasFog"></canvas>
<div id="back"></div>

<div class="user-info fixed-info hideonload noselect" style="height: auto;">
        <a href="{{ route('profile') }}">
          <div class=" my-font-regular t-spacing" >
          <!--<div class="separator"></div>-->
            <div class="icon" style="background : url({{ $u->avatar }}); border-radius : 50%; background-size : 100%;"></div>
            <span>{{ $u->username }}</span>
            <small>LVL</small>
            <span id="info-level">{{ $u->lvl }}</span>
            <!--<div class="separator"></div>-->
            </div>
          </a>
        </div>

<input type="checkbox" id="toggle">

<div class="chat-strip" id="menu-rsp">
<a id="sound-btn" class="interface-btn" ></a>
<label for="toggle" style="
top:15%;
right: 50%;
    margin-top:-75px;" >
	<div class="img"></div>
	<!--<img src="images/svg/chat.svg">-->

</label>

@include('right-side')
</div>
<aside class="sliding-panel">

	<div class="help-text" style="height:100%">
			<div class="col-md-12 my-font-regular style-3" id="chat" >

	            <div class="row current-chat-area">
	                <div class="col-md-12">
						<ul class="media-list" id="messages">
							@foreach($chat as $message)
							  <li class="media">
								  <div class="media-body">
									  <div class="media">
										  <a class="pull-left" href="#">
											  <div class="user-img chat-img" style="background-image: url({{ $message['avatar'] }})"></div>
										  </a>
										  <div class="media-body">
											  <span class="chat-name" style="cursor : pointer;">{{ $message['username'] }}</span>
											  <small>LVL</small><span class="chat-level">{{ $message['lvl'] }}</span><br>
											  <small class="chat-message">{{ $message['message'] }}</small>

										  </div>
									  </div>

								  </div>
							  </li>
						  @endforeach
					  </ul>
	                </div>
	            </div>

			</div>
	        <textarea  id="chat-input" placeholder="CHAT HERE" class="my-font"></textarea>
	<!--*******************<**************************************************-->
	</div>
</aside>













<div id="scene" >
<div  id="wrapper" class="layer noblur" data-depth="0.10" >




	<div class="container

	col-md-8 col-md-offset-2
	col-xs-10 col-xs-offset-1
	bandit-container p-top25">
	<!--col-md-8 col-md-offset-2 -->
		<div class="sep"></div>
		<div class="sep"></div>
		<div class="row">
			<div class="

			col-sm-6 col-sm-offset-3
			col-xs-8 col-xs-offset-2

			 bottom-space noselect" >
			 <a href="{{ route('home') }}"><img src="{{ asset('assets/frontend/images/min-logo.png') }}" alt="" style="width: 100%"></a>
			 </div>
		</div>
		<div class="row bottom-space rel" style="height:100%; text-align: center;">

			<div class="side-numbers my-font-regular noselect" style="right: 100%; margin-right: 50px;">
<a href="{{ route('slot.machine') }}">
<div class="back-arrow"></div>
</a>
</div>

            <style>
			.back-arrow{
				background-image: url('https://dotaregal.com/assets/frontend/images/svg/arrow_left.svg');
				background-size: 100% 100%;
				height: 150px;
				width: 30px;
				opacity: 0.1;
				transition: .5s;
			}
			.back-arrow:hover{
				opacity: 0.3;
				transition: .5s;
			}

            .pay-info{
                text-align: left;
                line-height: 0.5 ;
                padding-top: 30px;
                padding-left: 15px;
            }
            .pay-img{
                background-image: url(images/slot/2.png);
                background-repeat: no-repeat;
                background-size: contain;
                height: 100%;
            }
            .slot-element{
                background-image: none !important;
            }
            .slot{
                /*overflow-y: scroll !important;*/
            }
            .pay-header {
                font-size: 18pt;
            }
            .bandit-block{
                margin-top: 25px;
            }
			.pay-info {
				padding-top: 10px;
			}
            </style>
<span class="pay-header my-font-regular t-spacing">PAY TABLE</span>
			<div class="
			bandit-block ">

			@for($i = 0; $i < count($list)/3; $i++)
			<div class="slot">
				@for($u = $i*3; $u < ($i*3)+3; $u++)
					@if($u < count($list))
					<div class="slot-element col-xs-12 col-lg-10 col-lg-offset-1 p0">
						<div class="col-xs-4 p0 pay-img" style="background-image: url({{ $list[$u]->url }});">
						</div>
					   <div class="col-xs-8 p0 pay-info t-spacing">
						   @foreach($list[$u]->multiplier as $key => $m)
						   	@if($list[$u]->type < 1)
							<p>x{{ ($key+3) }} - {{ $list[$u]->bets[$key] }}</p>
							@elseif($list[$u]->type == 2)
							<p>x{{ ($key+3) }} - {{ $list[$u]->games[$key] }}</p>
							@elseif($list[$u]->type == 1)
							<p>WILD</p>
							@endif
						   @endforeach
						   @if($list[$u]->type == 0)
						   	NORMAL
							@elseif($list[$u]->type == 1)
							WILD
							@else
							FREE
							@endif
					   </div>
				   	</div>
					@endif
				@endfor
				@if($i > 2)
					<div class="slot-element col-xs-12 col-lg-10 col-lg-offset-1 p0">
						<div class="col-xs-4 p0 pay-img" style="background-image: url(http://dotaregal.com/assets/frontend/images/slot/bonus/none.png);">
						</div>
					   <div class="col-xs-8 p0 pay-info t-spacing">
							<p>BONUS</p>
							<p>BONUS</p>
							<p>BONUS</p>
							BONUS
					   </div>
				   	</div>
				@endif
			</div>
			@endfor


			</div>
		</div>


	</div>


</div>

	<img src="{{ asset('assets/frontend/images/totem-bandit.png') }}"  class="layer"  id="totem" data-depth="0.02" style="bottom: 0 !important;">

</div>



    <audio autoplay loop id="myAudio" preload="auto">
                  <source src="{{ asset('assets/frontend/sounds/tech-background.mp3') }}" type="audio/mpeg">
                Your browser does not support the audio element.
                </audio>

	<script src="{{ asset('assets/frontend/scripts/jquery.parallax.js') }}"></script>



    <style>
    .bandit-container{
        position: relative;
        transition: 1s;
        right:0;
    }
    </style>
	<script>
        $('#scene').parallax();






	</script>

    <script src="{{ asset('assets/frontend/scripts/content-chat-move.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/main.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/chat.js') }}"></script>

 <script src="{{ asset('assets/frontend/scripts/fog.js') }}"></script>

 <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
 <script src="{{ asset('assets/js/toastr.js') }}"></script>
 <script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
 <script src="{{ asset('assets/js/app.js') }}"></script>
 <script src="{{ asset('assets/js/chat.js') }}"></script>

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
