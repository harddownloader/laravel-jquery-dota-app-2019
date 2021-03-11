<!DOCTYPE html>
<html lang="en" class="poker-html">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title }} - {{ $config->sitename }}</title>
	{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/preload.css') }}"/>--}}


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/fonts.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/poker.css') }}">
	<script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}" charset="utf-8"></script>
	<script src="{{ asset('assets/js/jquery.session.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}" charset="utf-8"></script>


    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/fog.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/frontend/styles/pers.css') }}">

	<script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>

	<script>
		$(document).ready(function() {
			$('#myModalBet').on('shown.bs.modal', function() {
			    $(document).off('focusin.modal');
			});
		});
	</script>

	<script type="text/javascript">
		var PutVariable = 'ante';
		const LOSE_MSG = "{{ $lang['poker']['lose'] }}";
		const WIN_MSG = "{{ $lang['poker']['win'] }}";
		const NGAME_MSG = "{{ $lang['poker']['ngame'] }}";
		const IDENTITY_MSG = "{{ $lang['poker']['idcombo'] }}";
	</script>

	@if(Auth::check())
		<script type="text/javascript">
			const USER_ID = parseFloat('{{ $u->id }}');
			const USER_USERNAME = '{{ $u->username }}';
			const USER_STEAMID64 = '{{ $u->steamid64 }}';
			const USER_AVATAR = '{{ $u->avatar }}';
            const BET_TIMER = parseFloat('{{ $config->poker_bet_timer }}');
            const RAISE_TIMER = parseFloat('{{ $config->poker_raise_timer }}');
		</script>
	@endif

	@if(!is_null($game))
		<script type="text/javascript">
			const GAME_STATUS = '{{ $game->status }}';
			const ANTE = parseFloat('{{ $game->ante }}');
			const BLIND = parseFloat('{{ $game->blind }}');
			const TRIPS = parseFloat('{{ $game->trips }}');
			const BET = parseFloat('{{ $game->bet }}');
			var CARD_1 = ('{{ json_encode($userCards) }}').replace(/&quot;/g, '');
		</script>
	@else
		<script type="text/javascript">
			const GAME_STATUS = 0;
			const ANTE = 0;
			const BLIND = 0;
			const TRIPS = 0;
			const BET = 0;
			const CARD_1 = [];
			const CARD_2 = [];
		</script>
	@endif

	@if(Auth::check())
		<script src="{{ asset('assets/js/online.js') }}"></script>
	@endif

	<style>
		.character, .character2{
			transform: translate(-51%, -50%) scale(1);
			margin-left: 28px !important;
		}
		#myModalBids .modal-content{
			background-color: rgba(50, 50, 50, 0.85) !important;
		}
		#myModalRules, #myModalRules .modal-dialog{
			width: 700px;
			height: 500px;
		}

		#myModalRules .modal-body{
			text-align: left !important;
			padding: 0 35px 25px 35px !important;
		}

		#myModalRules .modal-dialog{
			margin-top: 0 !important;
		}

		#myModalRules .modal-content{
			min-height: 500px;
		}

		#myModalRules .modal-body{
			min-height: 290px;
		}

		#poker-line{
			background-color: none !important;
		}
        .modal{
            top: 15% !important;
            z-index: 998 !important;
        }
        .modal-dialog{
            top: 12% !important;
        }
        .z-index-logo{
            position: relative;
            z-index: 99999 !important;
        }
	</style>
</head>
<body>


{{--<div class="preload " id="preload-simple">--}}
  {{--<div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"--}}
  {{--id="preload-text">--}}
  {{--<img src="{{ asset('assets/images/loader.gif') }}"--}}
  {{--class="img-responsove vcenter" style="width: 100px;">--}}
  {{--</div>--}}
{{--</div>--}}
{{--<script src="{{ asset('assets/frontend/scripts/preload.js') }}"></script>--}}
{{--<script>simplePreload();</script>--}}

<canvas id="myCanvasFog"></canvas>
<div id="back"></div>

<div class="modal custom fade my-font-regular t-spacing hcenter"
 id="myModalBet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
        <span class="modal-title my-font" id="myModalLabel">{!! $lang['poker']['place'] !!}</h4>
      </div>
      <div class="modal-body row">

		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space" style="margin-left: 20px; margin-right:-20px;">
		  	<button type="button" class="btn btn-xs btn-poker btn-undo btn-paytable" onclick="$('#myModalTripsBlinds').modal('show');">{!! $lang['poker']['paytable'] !!}</button>
		  	<button type="button" class="btn btn-xs btn-poker btn-undo btn-paytable" onclick="$('#myModalRules').modal('show');">{!! $lang['poker']['rules'] !!}</button>
		  </div>

		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space">
		  <span>{!! $lang['poker']['bet'] !!} </span><span id="bet">0</span>
		  <div class="coin noselect"></div>
		  </div>
		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space">
		  <button type="button" class="btn btn-xs btn-poker btn-undo" style="margin-left: -60px;">{!! $lang['poker']['undo'] !!}</button>
		  <button type="button" class="btn btn-xs btn-poker btn-clear">{!! $lang['poker']['clear'] !!}</button>
		 </div>
        <div class="col-xs-12 pad hcenter ">
            <div class="bet poker_change_value chip-1" data-value="1"></div>
            <div class="bet poker_change_value chip-5" data-value="5"></div>

            <div class="bet poker_change_value chip-25" data-value="25"></div>

            <div class="bet poker_change_value chip-100" data-value="100"></div>
            <div class="bet poker_change_value chip-500" data-value="500"></div>



        </div>

		<div class="col-xs-5 col-xs-offset-1 pad hcenter">
			<div class="bet chip-img chip-null pad-text poker-multiplier" data-value="2" style="margin-left: 17px; margin-right: 17px;">2x</div>
			<div class="bet chip-img chip-null pad-text poker-multiplier" data-value="5">5x</div>

		</div>
		<div class="col-xs-5  pad hcenter">
			<div class="bet chip-img chip-null pad-text poker-multiplier" data-value="10">10x</div>

			<div class="bet chip-img chip-null pad-text poker-multiplier" data-value="20" style="margin-left: 17px; margin-right: 17px;">20x</div>
		</div>

        <div class="col-xs-12 pad hcenter my-font m-top10">
            <div class="sep"></div>
            <span id="bet-timer">{{ $config->poker_bet_timer }}</span>
            <div class="sep"></div>
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal custom fade my-font-regular t-spacing hcenter"
 id="myModalChoose" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
        <span class="modal-title my-font" id="myModalLabel">{!! $lang['poker']['take_dec'] !!}</h4>
      </div>
      <div class="modal-body row">
      <!-- <div class="col-xs-12 pad hcenter my-font-regular bottom-space">
            <span>YOU: </span>
            <span class="my-font" id="bet">4x</span>

        </div> -->
        <div class="col-xs-12 pad hcenter " id="checkResult">
            <div class="bet chip-3x"></div>
            <div class="bet chip-4x"></div>
            <div class="bet chip-check"></div>
            <div class="bet chip-fold"></div>



        </div>

        <div class="col-xs-12 pad hcenter my-font m-top10">
            <div class="sep"></div>
            <span id="check-timer">{{ $config->poker_raise_timer }}</span>
            <div class="sep"></div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal custom fade my-font-regular t-spacing hcenter"
 id="myModalBids" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
        <span class="modal-title my-font" id="myModalLabel">{!! $lang['poker']['accepted'] !!}</h4>
      </div>


    </div>
  </div>
</div>

<style type="text/css">

	#myModalTripsBlinds li {
		list-style : none;
	}

	.pay-card-table li{
		margin: 5px 0;
	}

	.pay-card-table li img{
		width: 80px;
	}

	#myModalTripsBlinds, #myModalTripsBlinds .modal-dialog{
		width: 600px;
	}

	#myModalTripsBlinds .modal-dialog{
		margin-top: -10px !important;
	}

	#myModalTripsBlinds .pay-card-table img{
		width: 150px !important;
	}
</style>




<div class="modal custom fade my-font-regular t-spacing hcenter" id="myModalRules" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content" style="background-color: rgba(50, 50, 50, 1);">
<br>
<div class="modal-body" style="max-height:200px; overflow-y:scroll; padding: 0 20px 25px 20px;
font-size: 13px;
letter-spacing: 0;">
<div class="btn-close close" onclick="$('#myModalRules').modal('hide');" style="font-size: 24px;
font-weight: 700;
line-height: 1;
color: #fff;
text-shadow: 0 1px 0 #fff; position:fixed; right: 10px; top: 10px;">&times;</div>
{!! $lang['poker']['rules_text'] !!}




</div>
</div>
</div>
</div>




<div class="modal custom fade my-font-regular t-spacing hcenter" id="myModalTripsBlinds" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="top: 0 !important; height: 650px;">
<div class="modal-dialog">
<div class="modal-content" style="background-color: rgba(50, 50, 50, 0.9);">
<br>
<div class="modal-body">
<div class="btn-close close" onclick="$('#myModalTripsBlinds').modal('hide');" style="position:absolute; top:0px; right:10px; z-index:500;">&times;</div>
<div class="row">


<div class="col-xs-5 col-xs-offset-1">
    <span class="modal-title my-font" id="myModalLabel">{!! $lang['poker']['blind'] !!}</span>
<br><br>
<div>
<ul style="padding : 0; text-align:left !important;" class="pay-card-table">
	<li><img src="{{ asset('assets/images/new-cards/10.png') }}"> - 500x</li>
	<li><img src="{{ asset('assets/images/new-cards/9.png') }}"> - 50x</li>
	<li><img src="{{ asset('assets/images/new-cards/8.png') }}"> - 10x</li>
	<li><img src="{{ asset('assets/images/new-cards/7.png') }}"> - 3x</li>
	<li><img src="{{ asset('assets/images/new-cards/6.png') }}"> - 1.5x</li>
	<li><img src="{{ asset('assets/images/new-cards/5.png') }}"> - 1x</li>
	<li><img src="{{ asset('assets/images/new-cards/4.png') }}"> - 0x</li>
	<li><img src="{{ asset('assets/images/new-cards/3.png') }}"> - 0x</li>
	<li><img src="{{ asset('assets/images/new-cards/2.png') }}"> - 0x</li>
	<li><img src="{{ asset('assets/images/new-cards/1.png') }}"> - 0x</li>
</ul>
</div>
</div>
    <div class="col-xs-5">
        <span class="modal-title my-font" id="myModalLabel">{!! $lang['poker']['trips'] !!}</span>
        <br><br>
<div>
<ul style="padding : 0; text-align:left !important;" class="pay-card-table">
	<li><img src="{{ asset('assets/images/new-cards/10.png') }}"> - 100x</li>
	<li><img src="{{ asset('assets/images/new-cards/9.png') }}"> - 40x</li>
	<li><img src="{{ asset('assets/images/new-cards/8.png') }}"> - 20x</li>
	<li><img src="{{ asset('assets/images/new-cards/7.png') }}"> - 7x</li>
	<li><img src="{{ asset('assets/images/new-cards/6.png') }}"> - 6x</li>
	<li><img src="{{ asset('assets/images/new-cards/5.png') }}"> - 5x</li>
	<li><img src="{{ asset('assets/images/new-cards/4.png') }}"> - 3x</li>
</ul>
</div>
    </div>
</div>
</div>
<br>
</div>
</div>
</div>





<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js"></script>


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
<a id="sound-btn" class="interface-btn" style="    position: absolute;
    right: 50%;
    margin-right: -13px;
    bottom: 25px;"></a>
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
	        <textarea  id="chat-input" placeholder="{!! $lang['chat']['input'] !!}" class="my-font"></textarea>
	<!--*******************<**************************************************-->
	</div>
</aside>












<div style="transform: translate(-50%, -50%); position:absolute; top: 50%; left: 50%; width: 100%;">
<div id="scene" >
<div  id="wrapper" class="layer noblur" data-depth="0.10" >


<div class="character" ></div>
<div class="character2" style="display : none;"></div>

	<div class="container
	col-lg-6 col-lg-offset-3
	col-md-8 col-md-offset-2
	col-xs-10 col-xs-offset-1
	" >


	<!--col-md-8 col-md-offset-2 -->
		<div class="row z-index-logo">
            <div class="

            col-sm-6 col-sm-offset-3
            col-xs-8 col-xs-offset-2

             bottom-space noselect" >
             <a href="{{ route('home') }}"><img src="{{ asset('assets/frontend/images/min-logo.png') }}" alt="" style="width: 100%"></a>
             </div>
        </div>

			</div>





</div>
    <div id="poker-line" class="layer my-font-regular"  data-depth="0.1">
    <div class="p0 mycoins t-spacing" id="coins-block" >
        <div class="col-xs-12 p0">
            <span>{!! $lang['poker']['mycoins'] !!}</span>
        </div>
        <div class="col-xs-12 p0" >
            <span id="mycoins">{{ number_format($u->money, 0, ' ', ' ') }}</span><div class="coin noselect"></div>
        </div>
    </div>

    <div class="p0 mycoins t-spacing" id="total-block" >
        <div class="col-xs-12 p0">
            <span>{!! $lang['poker']['total'] !!}</span>
        </div>
        <div class="col-xs-12 p0" >
            <span id="totalcoins">@if(is_null($game)) 0 @else {{ $game->total }} @endif</span><div class="coin noselect"></div>
        </div>
    </div>
	<div id="player-cards-block" style="@if(is_null($game)) display : none; @endif">
        <span class="t-spacing">{!! $lang['poker']['player'] !!}</span>
        <span class="t-spacing" id=user_win style="display:none;">{!! $lang['poker']['uwin'] !!}</span>
        <!-- <span class="t-spacing yellow-text">ANTE x1</span>
        <span class="t-spacing yellow-text">PLAY x1</span> -->
        <div id="player-cards" class="p0">
			@if(!is_null($game))
				@if($game->status == 1)
					@foreach($combo1['used'] as $card)
						@if(isset($card['used']))
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						@else
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						@endif
					@endforeach
					@for($i = count($combo1['used']); $i < 5; $i++)
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					@endfor
				@elseif($game->status == 2)
					@foreach($combo2['used'] as $card)
						@if(isset($card['used']))
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						@else
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						@endif
					@endforeach
					@for($i = count($combo2['used']); $i < 5; $i++)
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					@endfor
				@elseif($game->status == 3)
					@foreach($combo3['used'] as $card)
						@if(isset($card['used']))
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						@else
							<div class="card" style="background : url(./assets/images/cards/{{ $card['id'] }}_{{ $card['section'] }}.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						@endif
					@endforeach
					@for($i = count($combo3['used']); $i < 5; $i++)
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					@endfor
				@endif
			@endif
        </div>
		@if(!is_null($game))
			@if($game->status == 1)
			<span class="t-spacing" id="user-combo">{{ $combo1['name'] }}</span>
			@elseif($game->status == 2)
			<span class="t-spacing" id="user-combo">{{ $combo2['name'] }}</span>
			@elseif($game->status == 3)
			<span class="t-spacing" id="user-combo">{{ $combo3['name'] }}</span>
			@endif
		@else
			<span class="t-spacing" id="user-combo" style="display : none;">NONE</span>
		@endif
        <span class="t-spacing yellow-text" id="user-status" style="display : none;">{!! $lang['poker']['check'] !!}</span>
    </div>
    <div id="dealer-cards-block" style="display : none;">
        <span class="t-spacing">{!! $lang['poker']['dealer'] !!}</span>
		<span class="t-spacing" id=diler_win style="display:none;">{!! $lang['poker']['dwin'] !!}</span>
        <div id="player-cards" class="p0">
        </div>
        <span class="t-spacing yellow-text" id="dealer-combo" style="display:none;">NONE</span>
    </div>

    <div id="field">
        <div id="field-enemy-cards" class="filed-row p0" style="width: 40%; margin-left: 30%; ">
			@if(!is_null($game))
				<div class="card" style="background : url(./assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
				<div class="card" style="background : url(./assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
			@endif
        </div>
        <div id="game-cards" class="filed-row p0">
			@if(!is_null($game))
				@if($game->status == 2)
					@for($i = 0; $i < 3; $i++)
						<div class="card" style="background : url(./assets/images/cards/{{ $game->cards[2][$i]->id }}_{{ $game->cards[2][$i]->section }}.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
					@endfor
				@elseif($game->status == 3)
					@for($i = 0; $i < 5; $i++)
						<div class="card" style="background : url(./assets/images/cards/{{ $game->cards[2][$i]->id }}_{{ $game->cards[2][$i]->section }}.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
					@endfor
				@endif
			@endif
        </div>
        <div id="my-cards" class="filed-row p0" style="width: 40%; margin-left: 30%; margin-top: 7%">
			@if(!is_null($game))
				@foreach($game->cards[0] as $card)
					<div class="card" style="background : url(./assets/images/cards/{{ $card->id }}_{{ $card->section }}.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
				@endforeach
			@endif
        </div>
        <div class="anim-ball"></div>
    </div>

    <a class="myButton diamond-btn blue poker-play" id="play-btn" style="bottom: 3% !important;" >{!! $lang['poker']['play'] !!}</a>


    <a class="myButton diamond-btn poker-trips not_active" id="play-btn" style="bottom: 25%; left: 40%; z-index : 1001;">{!! $lang['poker']['trips'] !!}</a>
    <a class="myButton diamond-btn poker-ante blue" id="play-btn" style="bottom: 25%; z-index : 1001;">{!! $lang['poker']['ante'] !!}</a>
    <a class="myButton diamond-btn poker-blind" id="play-btn" style="bottom: 25%; left: 60%; z-index : 1001;">{!! $lang['poker']['blind'] !!}</a>
    <!-- <a class="myButton diamond-btn not-active blue" id="play-btn" style="bottom: 25%; left: 60%;" data-toggle="modal" data-target="#myModalChoose">BLIND</a> -->

    <!-- <a class="myButton diamond-btn" id="play-btn" style="bottom: 25%"><div class="chip-img chip-25"></div>&nbsp;</a> -->
    <!-- <a class="myButton diamond-btn blue" id="play-btn" style="bottom: 25%; left: 60%;"><div class="chip-img chip-100"></div>&nbsp;</a> -->


    </div>

	<!--<img src="images/poker/pers.png"  class="layer"  id="totem" data-depth="0.05" style="bottom: 0 !important;">-->

</div>
</div>


    <audio autoplay loop id="myAudio" preload="auto">
                  <source src="{{ asset('assets/frontend/sounds/poker_sound_cut.mp3') }}" type="audio/mpeg">
                Your browser does not support the audio element.
                </audio>

	<script src="{{ asset('assets/frontend/scripts/jquery.parallax.js') }}"></script>
		<script>
            $('#scene').parallax();
            $('.modal').modal({
                backdrop: false,
                keyboard: false,
                show: false
            });


            //$('#myModalBet').modal('show');
            //$('#myModalChoose').modal('show');
	</script>

	<script src="{{ asset('assets/frontend/scripts/main.js') }}"></script>
	<script src="{{ asset('assets/frontend/scripts/chat.js') }}"></script>

 {{-- <script src="{{ asset('assets/frontend/scripts/fog.js') }}"></script> --}}

 <script src="{{ asset('assets/frontend/scripts/pers.js') }}"></script>

 <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
 <script src="{{ asset('assets/js/toastr.js') }}"></script>
 <script src="{{ asset('assets/js/app.js') }}"></script>
 <script src="{{ asset('assets/js/poker.js') }}"></script>
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
