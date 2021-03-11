<!DOCTYPE html>
<html lang="en" class="profile-html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<title>{{ $title }} - {{ $config->sitename }}</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/styles/preload.css') }}"/>

	<script src="{{ asset('assets/frontend/scripts/jquery-3.2.0.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.session.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/fonts.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/style.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/mymedia.css') }}">

	<link rel="stylesheet" href="{{ asset('assets/frontend/styles/profile-var.css') }}">

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
    <!--<spam>The site is running in demo-version, but you still can sign-in and play right now! New games, trophies and a lot of great <br>changes are coming soon. <br>
    Stay tuned!</spam>
    <br><br>
    <span id="timeout">5</span> <spam>sec  remaining</spam>-->
  </div>
</div>
<script src="{{ asset('assets/frontend/scripts/preload.js') }}"></script>
<script>simplePreload();</script>



<input type="checkbox" id="toggle">

<div class="chat-strip" id="menu-rsp">
<a id="sound-btn" class="interface-btn" ></a>
@include('right-side')

</div>

	<div class="container">
		<div class="row">
			<div class="
			col-lg-4 col-lg-offset-4
			col-md-6 col-md-offset-3
			col-xs-10 col-xs-offset-1

			 bottom-space50" >
			 <a href="{{ route('home') }}"><img src="{{ asset('assets/frontend/images/min-logo.png') }}" alt="" style="width: 100%"></a>
			 </div>
		</div>
		<div class="row">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			 blur bottom-space" >

				<div class="col-xs-6 col-xs-offset-3 col-sm-3 col-sm-offset-0 col-md-2">
					<a href="https://steamcommunity.com/profiles/{{ $u->steamid64 }}" target="_blank" rel="nofollow noopener">
					<!--<img src="images/user1.png" alt="" class="vcenter" >-->
					<div id="profile-img" class="user-img" style="
				height: 125px;
				width: 125px;
				background: url({{ $u->avatar }}) no-repeat center center;
				background-size: auto 100%;
				margin:10px 0;
				margin-right: 25px;
				border-radius: 50%;
				border:5px solid white;"></div>
					</a>
					<div style="clear:both;"></div>
				</div>

				<div class="user-block col-xs-12 col-sm-5 col-md-4 align-middle" >

					<div style="vertical-align: middle; display: table-cell;">
						<span class="t-spacing" id="username">{{ $u->username }}</span>
						<a href="{{ route('logout') }}" class="myButton noselect" id="logout">{!! $lang['profile']['logout'] !!}</a><br
						<small>LEVEL</small>
						<div class="level-bar">
							<div class="toddler" id="level-progress" style="left : {{ round(($u->xp/$u->n_xp)*100, 2) }}%;"></div>
						</div>
						<span id="level">{{ $u->lvl }}</span>
					</div>

				</div>

				<div class="mycoins t-spacing
				col-xs-12 col-sm-4 col-md-4 col-md-offset-2
				align-middle" >
					<div>
						<div class="col-xs-12 col-md-6 p0">
							<span>{!! $lang['profile']['mycoins'] !!}</span>
						</div>
						<div class="col-xs-12 col-md-6">
							<span id="mycoins">{{ number_format($u->money, 0, ' ', ' ') }}</span><div class='coin noselect'></div>
						</div>

						<a href="{{ route('deposit') }}" class="myButton">{!! $lang['profile']['deposit'] !!}</a>
						<a href="{{ route('withdraw') }}" class="myButton">{!! $lang['profile']['withdraw'] !!}</a>
					</div>
				</div>

			</div>

		</div>

		<div class="row">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			 blur hcenter-md bottom-space strip"  >
				<div class="col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p>{!! $lang['profile']['gamesp'] !!}</p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0 col-xs-10 col-xs-offset-1">
					<p>{!! $lang['profile']['double'] !!} <span id="roulette">{{ $u->roulette }}</span></p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p>{!! $lang['profile']['poker'] !!} <span id="poker">{{ $u->poker }}</span></p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p>{!! $lang['profile']['bandit'] !!} <span id="jackpot">{{ $u->slot_machine }}</span></p>
				</div>
			 </div>
		</div>

		<style>

			.collection-list{
				text-align: center;
			}

			.collection-list h3{
				font-size: 14px;
				margin-top: 5px;
				text-transform: uppercase;
			}

			.collection-list p{
				margin-top: 5px;
				font-size: 13px;
			}

			.collection-title > li, .nav-pills > li, .rsp-center > li {
				float:none;
				display:inline-block;
				*display:inline; /* ie7 fix */
				zoom:1; /* hasLayout ie7 trigger */
			}

			.collection-title, .nav-pills {
				text-align:center;
			}
			.collection-title a{
				border: none;
				color: #4e4e4e;
				text-transform: uppercase;
			}
			.collection-title .active a{
				background: none !important;
				border: none !important;
				color: #b4aaa9 !important;
			}

			.rsp-center{
				border: none;
				border-bottom: 1px solid #b4aaa9;
				margin-top: 8px;
			}

			.rsp-center li .active a{
				color: #b4aaa9 !important;
				background: none;
			}

			.rsp-center .rsp-link a{
				background: none !important;
				border: none !important;
				color: #b4aaa9 !important;
				opacity: 0.5 !important;
			}

			.rsp-center .rsp-link.active a{
				opacity: 1 !important;
			}

			.collection-title{
				border-bottom: none;
			}

			.achievement-icon{
				text-align: center;
				position: relative;
			}

			.achievement-icon img{
				width: 100%;
				position: relative;
				left: 50%;
				margin-left: -35px;
				max-width: 70px;
			}

			.achievement-desc{
				text-transform: lowercase;
				font-size: 10px !important;
			}

			.collection-list h2{
				font-size: 16px;
				text-transform: uppercase;
				overflow: hidden;
				position: relative;
				border-bottom: 1px solid #b4aaa9;
				padding-bottom: 10px;
			}

			.nav-tabs>li>a{
				border: none;
			}

			.nav-tabs>li>a:hover{
				background: none !important;
				color: #fff;
				border: none;
			}

			#trophies .achievement-icon img{
				position: static;
				margin-left: 0 !important;
			}

			.collection-list-left .achievement-icon{
				text-align: left !important;
			}

			.collection-list-left .achievement-icon img:first-child{
				margin-right: 30px;
			}

		</style>

		<div class="row">



			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0 blur">
				<div style="padding: 20px 0 10px 0;">
				<!-- Nav tabs -->
				<ul class="collection-title nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a id="tab-collection" href="#collection" aria-controls="collection" role="tab" data-toggle="tab">{!! $lang['profile']['my_collection'] !!}</a></li>
					<li role="presentation"><a id="tab-trophies" href="#trophies" rel="nofollow noopener" aria-controls="trophies" role="tab" data-toggle="tab">{!! $lang['profile']['all_collection'] !!}</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="collection">

						<div class="collection-list">

							<div class="row">
								<div class="owl-carousel owl-theme">
								@foreach($achievements as $key => $achievement)
									@if($achievement->unlock)
										<div class="achievement-icon">
										<img src="/assets/images/prizes/{{ $achievement->img }}.png">
										<h3>{{ $achievement->name }}</h3>
										<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
										<!-- <p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p> -->
									</div>
									@endif
								@endforeach
							</div>
							</div>

						</div>

					</div>
					<div role="tabpanel" class="tab-pane" id="trophies">
						<div class="collection-list">
							<h2>{!! $lang['profile']['lvl_collection'] !!}</h2>
						<div class="row">
							@foreach($achievements as $key => $achievement)
								@if($achievement->category == "lvl")
									@if($achievement->unlock)
										<div class="col-sm-3 achievement-icon">
											<img src="/assets/images/prizes/{{ $achievement->img }}.png">
											<h3>{{ $achievement->name }}</h3>
											<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
											<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
										</div>
										@else
										<div class="col-sm-3 achievement-icon" style="opacity: 0.5;">
											<img src="/assets/images/prizes/{{ $achievement->img }}.png">
											<h3>{{ $achievement->name }}</h3>
											<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
											<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
										</div>
									@endif
								@endif
							@endforeach
						</div>
						</div>

						<div class="collection-list collection-list-left">
						<div class="row">

							<div class="col-sm-6">
								<h2>{!! $lang['profile']['gen_collection'] !!}</h2>

								<div style="max-height: 360px; overflow-y: scroll; overflow-x: hidden;">
								<div class="row">
								@foreach($achievements as $key => $achievement)
									@if($achievement->category == "other")
										@if($achievement->unlock)
											<div class="col-sm-12 achievement-icon">
												<div class="row">
													<div class="col-sm-3">
												<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
													</div>
													<div class="col-sm-5" style="margin-top: 22px;">
												<h3>{{ $achievement->name }}</h3>
												<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
													</div>
													<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
												</div>
												</div>
											</div>
											@else
											<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
												<div class="row">
													<div class="col-sm-3">
												<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
													</div>
													<div class="col-sm-5" style="margin-top: 22px;">
												<h3>{{ $achievement->name }}</h3>
												<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
													</div>
													<div class="col-sm-4" style="margin-top: 35px;">
												<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
											</div>
											</div>
											</div>
										@endif
									@endif
								@endforeach
								</div>
								</div>

							</div>
							<div class="col-sm-6">

								<div>

									<!-- Nav tabs -->
									<ul class="nav nav-tabs rsp-center" role="tablist">
										<li role="presentation" class="active rsp-link-active rsp-link"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">{!! $lang['profile']['r'] !!}</a></li>
										<li role="presentation" class="rsp-link"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">{!! $lang['profile']['s'] !!}</a></li>
										<li role="presentation" class="rsp-link"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">{!! $lang['profile']['p'] !!}</a></li>
									</ul>

									<div style="max-height: 360px; overflow-y: scroll; overflow-x: hidden;">

									<!-- Tab panes -->
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="home">
										@foreach($achievements as $key => $achievement)
											@if($achievement->category == "double")
												@if($achievement->unlock)
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
														<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													@else
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												@endif
											@endif
										@endforeach
										</div>
										<div role="tabpanel" class="tab-pane" id="profile">
										@foreach($achievements as $key => $achievement)
											@if($achievement->category == "bandit")
												@if($achievement->unlock)
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
																<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													@else
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												@endif
											@endif
										@endforeach
										</div>
										<div role="tabpanel" class="tab-pane" id="messages">
										@foreach($achievements as $key => $achievement)
											@if($achievement->category == "poker")
												@if($achievement->unlock)
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													@else
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/{{ $achievement->img }}.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3>{{ $achievement->name }}</h3>
														<p class="achievement-desc">{{ $lang['achievements'][$achievement->name]['desc'] }}</p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price">{{ $lang['achievements'][$achievement->name]['value'] }}<span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												@endif
											@endif
										@endforeach
										</div>
									</div>

									</div>

								</div>

							</div>

						</div>
						</div>
					</div>
				</div>

			</div>

		</div>
		</div>
		<br>

		<div class="row">
		<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			 blur hcenter bottom-space strip"  >

			<div class="col-sm-6 col-sm-offset-3
				col-xs-10 col-xs-offset-1 hcenter">
					<span>{!! $lang['profile']['free'] !!}</span><br>
					<small>{!! $lang['profile']['enter'] !!}</small>

					<form class="form-inline">
					 	<div class="col-sm-12">
							<input type="text" id="promoCode">
						</div>
						<button type="button" class="myButton" id="promoButton">{!! $lang['profile']['btn'] !!}</button>
					</form>

					<br>
					<small>Your referral code : {{ $u->ref }}</small>
				</div>

</div>
		</div>




	</div>
<audio autoplay loop id="myAudio" preload="auto">
				  <source src="{{ asset('assets/frontend/sounds/tech-background.mp3') }}" type="audio/mpeg">
				Your browser does not support the audio element.
				</audio>

	<script src="{{ asset('assets/frontend/scripts/bootstrap.min.js') }}"></script>

	<script src="{{ asset('assets/frontend/scripts/main.js') }}"> </script>
	<script src="{{ asset('assets/js/owl.carousel.min.js') }}"> </script>

	<link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
    <script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

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

      $('.owl-carousel').owlCarousel({
          loop:true,
                    margin:0,
                    nav:false,
          autoplay: true,
          autoplayTimeout: 3500,
          smartSpeed: 1200,
                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:3
                        },
                        1000:{
                            items:5
                        }
                    }
	  });


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
