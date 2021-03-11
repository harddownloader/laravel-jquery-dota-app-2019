<!DOCTYPE html>
<html lang="en" class="roulette-html">
<head>
    <meta http-equiv="Cache-Control" content="no-cache" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title }} - {{ $config->sitename }}</title>


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/fonts.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/mymedia.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/profile-var.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/parallax.css') }}">





  <script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}" charset="utf-8"></script>
	<script src="{{ asset('assets/js/jquery.session.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>

  <script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}" charset="utf-8"></script>

  <link rel="stylesheet" href="{{ asset('assets/frontend/styles/fog.css') }}">


  <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
  <script src="{{ asset('assets/js/toastr.js') }}"></script>
  <script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
  <script src="{{ asset('assets/js/chat.js') }}"></script>
  <script src="{{ asset('assets/js/double.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>

  @if(Auth::check())
  <script>const USER_ID = parseFloat('{{ $u->id }}');</script>
  @else
  <script>const USER_ID = null;</script>
  @endif

  @if(Auth::check())
	  <script type="text/javascript">
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
<body >



{{--<div class="preload " id="preload-simple">--}}
  {{--<div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"--}}
  {{--id="preload-text">--}}
  {{--<img src="{{ asset('assets/frontend/scripts/preload.js') }}"--}}
  {{--class="img-responsove vcenter" style="width: 100px;">--}}
    {{--<!--<spam>The site is running in demo-version, but you still can sign-in and play right now! New games, trophies and a lot of great <br>changes are coming soon. <br>--}}
    {{--Stay tuned!</spam>--}}
    {{--<br><br>--}}
    {{--<span id="timeout">5</span> <spam>sec  remaining</spam>-->--}}
  {{--</div>--}}
{{--</div>--}}
{{--<script src="{{ asset('assets/frontend/scripts/preload.js') }}"></script>--}}
{{--<script>simplePreload();</script>--}}


<canvas id="myCanvasFog"></canvas>
<!--
<script>
var element = $('aside').first().detach();
$('.chat-strip').first().append(element);

</script>
-->

<!--<a href="profile.html" id="steam-link"><img src="images/steam.png"></a>-->

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
<div class="help-text" style="height : 100%;">

<!--     <br><br> -->
    <!--<h2>Get your watch's model from its papers!</h2>
    <p>If you still have the watch's original papers and books, and you're not sure of the watch's exact model name or number, you can usually find that information in the papers.</p>
    <p>So we suggest you refer to the papers in order to fill in the following boxes.</p>-->


    <!--*********************************************************************-->
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
        <textarea  id="chat-input" placeholder="{!! $lang['chat']['input'] !!}" class="my-font"></textarea>
<!--*********************************************************************-->
</div>
</aside>
<!--
<div class="chat-strip">
	<input type="checkbox" id="toggle">

	<label for="toggle">?</label>
	<div class="chat-btn " for="toggle">
		<img src="images/svg/chat.svg">
	</div>

	<aside class="sliding-panel"> <!--chat-panel
	<div class="return-arrow">
  </div>
	<div class="help-text" >asd</div>
	</aside>
</div>
-->

<div id="scene" >
		<div  id="wrapper" class="layer noblur" data-depth="0.10" >



	<div class="container col-xl" >
		<div class="row">
			<div class="
			col-lg-2 col-lg-offset-5
			col-md-6 col-md-offset-3
			col-xs-10 col-xs-offset-1

			 bottom-space noselect" >
			 <a href="{{ route('home') }}"><img src="{{ asset('assets/frontend/images/min-logo.png') }}" alt="" style="width: 100%"></a>
			 </div>
		</div>
		<div class="row">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			roulette" >

				<div class="
					col-md-8 col-md-offset-2
					col-xs-10 col-xs-offset-1
				">
					<img src="{{ asset('assets/frontend/images/roulette.png') }}" alt="" class="vcenter noselect">
					<img src="{{ asset('assets/frontend/images/circle.png') }}" alt="" class="vcenter noselect"
					style="height: 250px; width:250px; position: absolute; top:0; left:50%; margin-left: -125px; transform : rotate({{ $rotate }}deg;" id="circle">
					<div class="my-font"
					style="position: absolute; top:0; left:50%; top:50%; width:50px; height: 30px; margin-left: -10px; margin-top: -30px">
					<span class="timer">30</span></div>
					<div style="clear:both;"></div>
				</div>

				<div class="user-info t-spacing
				col-xs-3 col-xs-offset-1">


					<a href="{{ route('profile') }}" >
					<div class="block my-font">
					<div class="separator"></div>
						<div class="icon" style="background : url({{ $u->avatar }}); background-size : 100%; border-radius : 50%;"></div>
						<span>{{ $u->username }}</span>
						<small>LVL</small>
						<span id="info-level">{{ $u->lvl }}</span>
						<br><br>

						<span class="balance">{{ number_format($u->money, 0, ' ', ' ') }}</span><div class='coin noselect'></div>
						<div class="separator"></div>
					</div>
                    </a>
				</div>

			<div class="calc  my-font t-spacing
				col-xs-3 col-xs-offset-8">



					<div class="block">
					<div class="separator"></div>

					<input type="number" name="c-numbers"  id="calc-number" value="0">
					<div class='coin noselect'></div><br><br>
					<div class="calc-panel noselect">
						<div><span data-method="plus" data-value="100">+100</span> <div id="circle1" class="circle" data-type="blue"></div></div>
						<div><span data-method="plus" data-value="1000">+1K</span><div id="circle2" class="circle" data-type="green"></div></div>
						<div><span data-method="plus" data-value="10000">+10K</span><div id="circle3" class="circle" data-type="yellow"></div></div>
						<div><span data-method="max" data-value="max">MAX</span><div id="circle4" class="circle" data-type="red"></div></div>
					</div>

						<div class="separator"></div>
					</div>


				</div>


			</div>

		</div>

		<div class="row my-font-regular">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			statistics t-spacing hcenter "  >


			 <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-0 col-md-3">
			 <span>2x</span>

			 	<table class="table bets-list" data-type="blue">
					<thead>
  					<tr class="top_bet" data-type="blue">
    				      <th class="top_user">{{ $top['blue']['count'] }} {!! $lang['roulette']['users'] !!}</th>
    				      <th class="top_value">{{ $top['blue']['value'] }}</th>
    				    </tr>
  				  </thead>
  				  <tbody class="bets" data-type="blue">
                        <!-- foreach -->
                        @foreach($bets as $bet)
                            @if($bet->type == 'blue')
                                <tr data-value="{{ $bet->value }}">
                				     <td style="white-space : nowpar; overflow : hidden; @if(Auth::check() && $bet->user_id == $u->id) color : yellow; @endif">{{ $bet->username }}</td>
                				     <td>{{ $bet->value }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <!-- end foreach -->
  				  </tbody>
				</table>

			 </div>
			 <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-0 col-md-3">
			 <span>3x</span>
			 	<table class="table bets-list" data-type="green">
					<thead>
					  <tr class="top_bet" data-type="green">
						<th class="top_user">{{ $top['green']['count'] }} {!! $lang['roulette']['users'] !!}</th>
						<th class="top_value">{{ $top['green']['value'] }}</th>
					  </tr>
					</thead>
					<tbody class="bets" data-type="green">
						<!-- foreach -->
						@foreach($bets as $bet)
							@if($bet->type == 'green')
								<tr data-value="{{ $bet->value }}">
								   <td style="white-space : nowpar; overflow : hidden; @if(Auth::check() && $bet->user_id == $u->id) color : yellow; @endif">{{ $bet->username }}</td>
								   <td>{{ $bet->value }}</td>
								</tr>
							@endif
						@endforeach
						<!-- end foreach -->
					</tbody>
				</table>

			 </div><div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-0 col-md-3">
			 <span>5x</span>
			 	<table class="table bets-list" data-type="yellow">
					<thead>
					<tr class="top_bet" data-type="yellow">
						<th class="top_user">{{ $top['yellow']['count'] }} {!! $lang['roulette']['users'] !!}</th>
						  <th class="top_value">{{ $top['yellow']['value'] }}</th>

					</tr>
				  </thead>
				  <tbody class="bets" data-type="yellow">
						<!-- foreach -->
						@foreach($bets as $bet)
							@if($bet->type == 'yellow')
								<tr data-value="{{ $bet->value }}">
								   <td style="white-space : nowpar; overflow : hidden; @if(Auth::check() && $bet->user_id == $u->id) color : yellow; @endif">{{ $bet->username }}</td>
								   <td>{{ $bet->value }}</td>
								</tr>
							@endif
						@endforeach
						<!-- end foreach -->
				  </tbody>
				</table>

			 </div><div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-0 col-md-3">
			 <span>10x</span>
			 	<table class="table bets-list" data-type="red">
					<thead>
					<tr class="top_bet" data-type="red">
						<th class="top_user">{{ $top['red']['count'] }} {!! $lang['roulette']['users'] !!}</th>
						  <th class="top_value">{{ $top['red']['value'] }}</th>

					</tr>
				  </thead>
				  <tbody class="bets" data-type="red">
						<!-- foreach -->
						@foreach($bets as $bet)
							@if($bet->type == 'red')
								<tr data-value="{{ $bet->value }}">
								   <td style="white-space : nowpar; overflow : hidden; @if(Auth::check() && $bet->user_id == $u->id) color : yellow; @endif">{{ $bet->username }}</td>
								   <td>{{ $bet->value }}</td>
								</tr>
							@endif
						@endforeach
						<!-- end foreach -->
				  </tbody>
				</table>

			 </div>
			 </div>
		</div>
	</div>

	</div>


	<img src="{{ asset('assets/frontend/images/totem.png') }}"  class="layer"  id="totem" data-depth="0.02" style="bottom: 0 !important;">


	</div>


    <audio autoplay loop id="myAudio" preload="auto">
                  <source src="{{ asset('assets/frontend/sounds/roulette-environment.mp3') }}" type="audio/mpeg">
                Your browser does not support the audio element.
                </audio>


	<script src="{{ asset('assets/frontend/scripts/jquery.parallax.js') }}"></script>
    <script src="{{ asset('assets/frontend/scripts/fog.js') }}"></script>

    <script src="{{ asset('assets/frontend/scripts/main.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/chat.js') }}"></script>

	<script>
					$('#scene').parallax();
	</script>
	<script>
		$(".statistics div table tr td:last-child, .statistics div table tr th:last-child").append("<div class='coin noselect'></div>");
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
