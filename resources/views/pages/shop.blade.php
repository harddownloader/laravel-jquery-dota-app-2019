<!DOCTYPE html>
<html lang="en" class="shop-html">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<title>{{ $title }} - {{ $config->sitename }}</title>
	{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/preload.css') }}"/>--}}

	<script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.session.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>


	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/fonts.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/mymedia.css') }}">

	<!-- <link rel="stylesheet" href="{{ asset('assets/frontend/scripts/css/bootstrap-slider.min.css') }}"> -->
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/shop.css') }}">


		<link rel="stylesheet" href="{{ asset('assets/frontend/styles/normalize.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/ion-rs/css/ion.rs.css') }}">
	    <link rel="stylesheet" href="{{ asset('assets/ion-rs/css/ion.rs.skinFlat.css') }}">
	<!--<link rel="stylesheet" href="styles/profile-var.css">
	<link rel="stylesheet" href="styles/parallax.css">-->
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/pagination.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/mySlider.css') }}">

	<script src="{{ asset('assets/ion-rs/js/ion.rs.js') }}"></script>
    <script src="{{ asset('assets/ion-rs/js/ion.rs.min.js') }}"></script>

	<link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
	<script src="{{ asset('assets/js/app.js') }}"></script>

    <script type="text/javascript">
        var input = undefined;
    </script>

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

	@if(Auth::check() && $u->permission == 2)
	<script src="{{ asset('assets/js/admin_shop.js') }}"></script>
	@else
	<script src="{{ asset('assets/js/shop.js') }}"></script>
	@endif
	<script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
</head>
<body>

<style>
	.shop-item{
		height: 205px !important;
	}
	.img-scale img{
		transition: 0.5s;
	}
	.img-scale:hover img{
		transform: scale(1.2);
		transition: 0.5s;
	}
	.shop-user-info a:hover{
		text-decoration: none;
	}
</style>

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




<style>

</style>
<!-- Button trigger modal -->



<div class="modal custom fade my-font-regular t-spacing hcenter" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <span class="modal-title my-font" id="myModalLabel">{!! $lang['shop']['upd_trade'] !!}</h4>
      </div>
      <div class="modal-body row">

		<div class="col-xs-12 col-md-10">
			<input type="text" id="trade-input" placeholder="{!! $lang['shop']['enter_trade'] !!}" value="{{ $u->trade }}">
		</div>
		<div class="col-xs-12 col-md-2">
			<input type="button" value="{!! $lang['shop']['add_trade'] !!}" id="addUrl">
		</div>

		<script type="text/javascript">
			$('#addUrl').click(function() {
				$.ajax({
					url : '/saveUrl',
					type : 'post',
					data : {
						url : $('#trade-input').val()
					},
					success : function (data) {
						if(typeof data.msg == 'undefined') return;
						if(data.success) toastr.success(data.msg);
						if(!data.success) toastr.error(data.msg);
					},
					error : function (data) {
						toastr.error('Ошибка при отправке данных на сервер!');
						console.log(data.responseText);
					}
				});
			});
		</script>

		<div class="col-xs-12 pad">
			<div class="col-xs-8 pad">
				<small>{!! $lang['shop']['upd_trade1'] !!}</small>
			</div>
			<div class="col-xs-4 pad">
				<a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank"><small>{!! $lang['shop']['upd_trade2'] !!}</small></a>
			</div>
		</div>
		<div class="col-xs-12 pad">
			<div class="col-xs-8 pad">
				<small>{!! $lang['shop']['upd_trade3'] !!}</small>
			</div>
			<div class="col-xs-4 pad">
				<a href="https://steamcommunity.com/id/me/tradeoffers/privacy#trade_offer_access_url" target="_blank"><small>{!! $lang['shop']['upd_trade4'] !!}</small></a>
			</div>
		</div>&nbsp;
      </div>

    </div>
  </div>
</div>



<input type="checkbox" id="toggle">

<div class="chat-strip" id="menu-rsp">
<a id="sound-btn" class="interface-btn" ></a>
@include('right-side')

</div>



	<div class="container" >
		<div class="row">
			<div class="
			col-md-12 col-md-offset-0
			col-xs-10 col-xs-offset-1
			  " style=" min-height: 100vh; padding: 0">


				<img src="{{ asset('assets/frontend/images/head.png') }}" class="noselect" style="width:100%; height: 100%; position: absolute;left:0; top:0; z-index: 5">

				<!--pers-->
				<img src="{{ asset('assets/frontend/images/pers1.png') }}" id="pers1" class="noselect">
				<img src="{{ asset('assets/frontend/images/pers2.png') }}" id="pers2" class="noselect">

				<!--slider
				<div id="slider_container" class="noselect">
				  <div id="slider">
				    <ul>
				      <li><img src="https://uploadcare.cmtt.ru/7c8eccd4-4cb9-4d7b-916a-bc9c5fc3764a/" title="meow" /></li>
				      <li><img src="http://wallpapercave.com/wp/W0AytWw.jpg" title="meow" /></li>
				      <li><img src="https://s-media-cache-ak0.pinimg.com/originals/ad/66/c6/ad66c60728d322a62114ea167b07e816.jpg" title="meow" /></li>
				      <li><img src="http://www.wallpaperup.com/uploads/wallpapers/2015/07/09/746944/big_thumb_7f5bcf6eec4d4a5a0a47275c7abf6938.jpg" title="meow" /></li>
				      <li><img src="http://cdn.pcwallart.com/images/dota-2-heroes-wallpaper-nevermore-wallpaper-4.jpg" title="meow" /></li>
				    </ul>
				  </div>
				</div>
-->



				<div id="mySlider" class="noselect" interval="4000">
					<div class="mySlide"  id="myCurrentSlide"
					style="background-image: url(https://uploadcare.cmtt.ru/7c8eccd4-4cb9-4d7b-916a-bc9c5fc3764a/);"></div>
					<div class="mySlide"
					 style="background-image: url(https://wallpapercave.com/wp/W0AytWw.jpg);"></div>
					<div class="mySlide"
					 style="background-image: url(https://s-media-cache-ak0.pinimg.com/originals/ad/66/c6/ad66c60728d322a62114ea167b07e816.jpg);"></div>
					<div class="mySlide"
					 style="background-image: url(https://www.wallpaperup.com/uploads/wallpapers/2015/07/09/746944/big_thumb_7f5bcf6eec4d4a5a0a47275c7abf6938.jpg);"></div>
					<div class="mySlide"
					 style="background-image: url(https://cdn.pcwallart.com/images/dota-2-heroes-wallpaper-nevermore-wallpaper-4.jpg);"></div>
				</div>


				<!--user-info-->
				<div class="col-xs-6 col-xs-offset-3
				shop-user-info my-font-regular" >
					<div class="row">
						<div class="col-xs-12 col-md-6">
							<div class="col-xs-12 col-md-12">
							<a href="{{ route('profile') }}"><img src="{{ $u->avatar }}" alt="" class="img-responsive noselect" style="border-radius : 50%; margin-right: 2px; margin-top: -2px;">
								<small id="user-name" style="color: #c3bcbb;">{{ $u->username }}</small>
							</a>
							</div>
						</div>
						<div class="col-xs-12 col-md-6" >
							<small>{!! $lang['shop']['mycoins'] !!}</small>

							<div style="display: inline-block;">
							<span id="user-coins" class="balance">{{ number_format($u->money, 0, ' ', ' ') }}</span><div class='coin noselect'></div>
							</div>
						</div>
						</div>

				</div>



				<!-- <div class="buttonBox">
				  <span onclick="prev()">Previous</span>
				  <ul id="slider_btns">
				    <li onclick="page(1)"></li>
				    <li onclick="page(2)"></li>
				    <li onclick="page(3)"></li>
				    <li onclick="page(4)"></li>
				    <li onclick="page(5)"></li>
				  </ul>
				  <span onclick="next()">Next</span>
				</div> -->




				<!--logo-block-->
				<div class="col-xs-6 col-xs-offset-3" style=" z-index: 6;">
					<a href="{{ route('home') }}">
					<div id="logo-img" style="background:url({{ asset('assets/frontend/images/logo.png') }}) no-repeat; background-size: contain;">
					<!--<small class="my-font noselect"><br>ROYAL<br>BANK</small>-->
					</div></a>
				</div>

				<!--main-links-->
				<div class="link" id="link1" >
					<a href="{{ route('deposit') }}"><span class="my-font-regular  t-spacing">{!! $lang['shop']['deposit'] !!}</span></a>
					<div class="sep"></div>
				</div>
				<div class="link active-link" id="link2">
					<a href="{{ route('withdraw') }}"><span class="my-font-regular  t-spacing">{!! $lang['shop']['withdraw'] !!}</span></a>
					<div class="sep"></div>
				</div>






			 </div>
		</div>
		<div class="row">
			<div class="
				col-md-12 col-md-offset-0
				col-xs-10 col-xs-offset-1
			" style="background-color: transparent; height: 350px; padding: 0;">

				<div id="shop-left-panel" class="col-xs-3 col-md-2 my-font-regular">
					<button type="button" class="myButton" id="btn1">{!! $lang['shop']['check_steam'] !!}</button>

					<form>
  					<div class="form-group">
	  					<label for="item-name">{!! $lang['shop']['search'] !!}</label>
						<input type="text" name="item-name" placeholder="item name" id="search">
					</div>
					<div class="form-group">
							<div class="col-xs-12">
							<label>{!! $lang['shop']['by_price'] !!}</label>
							</div>
							<div class="col-xs-5">
								<input type="text" name="from" placeholder="from" id="price_from">
							</div>
							<div class="col-xs-5  col-xs-offset-2 bottom-space5">
								<input type="text" name="to" placeholder="to" id="price_to">
							</div>
							<div class="col-xs-12 bottom-space">
							<!--<input id="ex12c" type="text"/>-->


							<input type="text" id="price_input" >
							<!--<style>.js-irs-0{margin-top: -10px;}</style>-->

							</div>

  					</div>
  					<br>
					<div class="form-group">
						<label>{!! $lang['shop']['by_rarity'] !!}</label>
						<select class="selectpicker" id="raritys">
                            <option>{!! $lang['shop']['all'] !!}</option>
						</select>
					</div>
					<div class="form-group">
						<label>{!! $lang['shop']['by_type'] !!}</label>
						<select class="selectpicker" id="types">
                            <option>{!! $lang['shop']['all'] !!}</option>
						</select>
					</div>
					<div class="form-group">
						<label>{!! $lang['shop']['by_heroes'] !!}</label>
						<select class="selectpicker" id="heroes">
                            <option>{!! $lang['shop']['all'] !!}</option>
						</select>
					</div>
					<div class="form-group">
						<label>{!! $lang['shop']['by_quality'] !!}</label>
						<select class="selectpicker" id="qualitys">
                            <option>{!! $lang['shop']['all'] !!}</option>
						</select>
					</div>
					<div class="form-group">
						<label>{!! $lang['shop']['by_price'] !!}</label>
						<select class="selectpicker" id="sort">
							<option value="desc">{!! $lang['shop']['desc'] !!}</option>
                            <option value="asc">{!! $lang['shop']['asc'] !!}</option>
						</select>
					</div>

					<button type="button" class="myButton search_button" id="btn3">{!! $lang['shop']['search2'] !!}</button>

  					</form>

				</div>

				<div id="shop-right-panel" class="col-xs-3 col-md-2 my-font-regular">
					<button type="button" class="myButton trade-bbutton" id="btn2"  data-toggle="modal" data-target="#myModal2">{!! $lang['shop']['upd_trade'] !!}</button>

					<div class="col-xs-10 col-xs-offset-1 bottom-space5">

						<div class="col-xs-9">
							<span>{!! $lang['shop']['selected'] !!} [<span style="color : #fff; cursor: pointer;" class="removeAllItems">x</span>]</span>
						</div>
						<div class="col-xs-3 hright" id="count">
							0
						</div>
						</div>
					<div class="col-xs-10 col-xs-offset-1 bottom-space">

					<div class="col-xs-3">
							<span>{!! $lang['shop']['cost'] !!}</span>
						</div>
						<div class="col-xs-9 hright">
							&nbsp;<span id="cost-coins">0</span><div class='coin noselect'></div>
						</div>
					</div>


					<div class="col-xs-10 col-xs-offset-1" style="height: 60%" id="selected_items">

						<!-- <div class="col-xs-10 col-xs-offset-1  shop-item t-spacing">
							<img src="{{ asset('assets/frontend/images/item.png') }}" class="img-responsive bottom-space5">
							<span class="item-name">Fail 2016</span><br>
							<span class="item-type bottom-space5">Treasure</span><br>
							<span class="item-price">10600</span><div class='coin noselect'></div>
						</div> -->
					</div>


					<button type="button" class="myButton sendOffer" id="btn4">{!! $lang['shop']['withdraw_btn'] !!}</button>



				</div>
				<div id="shop-central-panel" class=" my-font-regular">
					<div class="col-xs-12 hcenter bottom-space5">
						<span>{!! $lang['shop']['artifacts'] !!} <span id="all_count">(0)</span></span><br>

					</div>
					<!-- <div class="col-xs-12 ">
						<label id="orderby">Order by:
						<select class="selectpicker" style="width:100px">
						  <option>Popularity</option>
						  <option>Quantity</option>
						  <option>Price</option>
						</select></label>
					</div> -->

					<div class="col-xs-12" id="shop-container" style="text-align: center;">
						<!-- items -->
					</div>

				<div id="pagination"></div>

				</div>
				<!--lines-->
				<img src="{{ asset('assets/frontend/images/line.png') }}" class="noselect"
				style="height: 100%; width: 2.4%;
				 position:absolute; left:15.2%">
				 <img src="{{ asset('assets/frontend/images/line.png') }}" class="noselect"
				style="height: 100%; width: 2.2%;
				 position:absolute; right:15.2%">

			</div>
		</div>

	</div>

<audio autoplay loop id="myAudio" preload="auto">
				  <source src="{{ asset('assets/frontend/sounds/tech-background.mp3') }}" type="audio/mpeg">
				Your browser does not support the audio element.
				</audio>
	<script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}"></script>

	<script src="{{ asset('assets/frontend/scripts/jquery.parallax.js') }}"></script>

	<script src="{{ asset('assets/frontend/scripts/bootstrap-slider.min.js') }}"></script>

	<script src="{{ asset('assets/frontend/scripts/ion.rangeSlider.min.js') }}"></script>



	<script src="{{ asset('assets/frontend/scripts/main.js') }}"> </script>


	<script src="{{ asset('assets/frontend/scripts/myslider.js') }}"></script>

	<script>
	boundLines();
	$("#shop-central-panel").bind("DOMSubtreeModified",boundLines);
	function boundLines(){
	$("img[src='images/line.png']").css(
	{"height":$("#shop-central-panel").css("height")});
	}
	</script>

	<script>
	$("#ex12c").slider({ id: "slider12c", min: 0, max: 1200, range: true, value: [2, 1114] });

	setTimeout(function(){
						console.log($(document).scrollTop()+1);
						if($(document).scrollTop()< 500)
					$('html, body').animate({
					                    scrollTop: $('#shop-central-panel').offset().top-60
					                }, 2000);
					},4000);


					$(".quantity .substraction").click(function(){
var $inp= $(this).parent().find("input");

$inp.prop("value",$inp.prop("value")-1);
if($inp.prop("value")<=0)
$inp.prop("value",1);
});
$(".quantity .addition").click(function(){
var $inp= $(this).parent().find("input");
$inp.prop("value",$inp.prop("value")-(-1));
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
