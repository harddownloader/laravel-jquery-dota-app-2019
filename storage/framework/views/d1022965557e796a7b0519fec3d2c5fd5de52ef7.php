<!DOCTYPE html>
<html lang="en" class="profile-html">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<title><?php echo e($title); ?> - <?php echo e($config->sitename); ?></title>

	<?php /*<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/frontend/styles/preload.css')); ?>"/>*/ ?>

	<script src="<?php echo e(asset('assets/frontend/scripts/jquery-3.2.0.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/jquery.session.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/jquery.cookie.js')); ?>"></script>

	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fonts.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bootstrap.min.css')); ?>">

	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/style.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/owl.carousel.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/mymedia.css')); ?>">

	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/profile-var.css')); ?>">

	<?php if(Auth::check()): ?>
		<script type="text/javascript">
			const USER_ID = parseFloat('<?php echo e($u->id); ?>');
			const USER_USERNAME = '<?php echo e($u->username); ?>';
			const USER_STEAMID64 = '<?php echo e($u->steamid64); ?>';
			const USER_AVATAR = '<?php echo e($u->avatar); ?>';
		</script>
	<?php endif; ?>

	<?php if(Auth::check()): ?>
		<script src="<?php echo e(asset('assets/js/online.js')); ?>"></script>
	<?php endif; ?>
	<script src="<?php echo e(asset('assets/js/socket.io-1.3.5.js')); ?>"></script>

</head>
<body>


<?php /*<div class="preload " id="preload-simple">*/ ?>
  <?php /*<div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"*/ ?>
  <?php /*id="preload-text">*/ ?>
  <?php /*<img src="<?php echo e(asset('assets/frontend/scripts/preload.js')); ?>"*/ ?>
  <?php /*class="img-responsove vcenter" style="width: 100px;">*/ ?>
    <?php /*<!--<spam>The site is running in demo-version, but you still can sign-in and play right now! New games, trophies and a lot of great <br>changes are coming soon. <br>*/ ?>
    <?php /*Stay tuned!</spam>*/ ?>
    <?php /*<br><br>*/ ?>
    <?php /*<span id="timeout">5</span> <spam>sec  remaining</spam>-->*/ ?>
  <?php /*</div>*/ ?>
<?php /*</div>*/ ?>
<?php /*<script src="<?php echo e(asset('assets/frontend/scripts/preload.js')); ?>"></script>*/ ?>
<?php /*<script>simplePreload();</script>*/ ?>



<input type="checkbox" id="toggle">

<div class="chat-strip" id="menu-rsp">
<a id="sound-btn" class="interface-btn" ></a>
<?php echo $__env->make('right-side', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>

	<div class="container">
		<div class="row">
			<div class="
			col-lg-4 col-lg-offset-4
			col-md-6 col-md-offset-3
			col-xs-10 col-xs-offset-1

			 bottom-space50" >
			 <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/frontend/images/min-logo.png')); ?>" alt="" style="width: 100%"></a>
			 </div>
		</div>
		<div class="row">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			 blur bottom-space" >

				<div class="col-xs-6 col-xs-offset-3 col-sm-3 col-sm-offset-0 col-md-2">
					<a href="https://steamcommunity.com/profiles/<?php echo e($u->steamid64); ?>" target="_blank" rel="nofollow noopener">
					<!--<img src="images/user1.png" alt="" class="vcenter" >-->
					<div id="profile-img" class="user-img" style="
				height: 125px;
				width: 125px;
				background: url(<?php echo e($u->avatar); ?>) no-repeat center center;
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
						<span class="t-spacing" id="username"><?php echo e($u->username); ?></span>
						<a href="<?php echo e(route('logout')); ?>" class="myButton noselect" id="logout"><?php echo $lang['profile']['logout']; ?></a><br
						<small>LEVEL</small>
						<div class="level-bar">
							<div class="toddler" id="level-progress" style="left : <?php echo e(round(($u->xp/$u->n_xp)*100, 2)); ?>%;"></div>
						</div>
						<span id="level"><?php echo e($u->lvl); ?></span>
					</div>

				</div>

				<div class="mycoins t-spacing
				col-xs-12 col-sm-4 col-md-5 col-md-offset-2
				align-middle" >
					<div>
						<div class="col-xs-12 col-md-6 p0">
							<span><?php echo $lang['profile']['mycoins']; ?></span>
						</div>
						<div class="col-xs-12 col-md-6">
							<span id="mycoins"><?php echo e(number_format($u->money, 0, ' ', ' ')); ?></span><div class='coin noselect'></div>
						</div>

						<a href="<?php echo e(route('deposit')); ?>" class="myButton"><?php echo $lang['profile']['deposit']; ?></a>
						<a href="<?php echo e(route('withdraw')); ?>" class="myButton"><?php echo $lang['profile']['withdraw']; ?></a>
					</div>
				</div>

			</div>

		</div>

		<div class="row">
			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0

			 blur hcenter-md bottom-space strip">
				<div class="col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p><?php echo $lang['profile']['gamesp']; ?></p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0 col-xs-10 col-xs-offset-1">
					<p><?php echo $lang['profile']['double']; ?> <span id="roulette"><?php echo e($u->roulette); ?></span></p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p><?php echo $lang['profile']['poker']; ?> <span id="poker"><?php echo e($u->poker); ?></span></p>
				</div>
				<div class="t-spacing col-sm-3 col-sm-offset-0
				col-xs-10 col-xs-offset-1">
					<p><?php echo $lang['profile']['bandit']; ?> <span id="jackpot"><?php echo e($u->slot_machine); ?></span></p>
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

			.col-xs-5ths,
			.col-sm-5ths,
			.col-md-5ths,
			.col-lg-5ths {
				position: relative;
				min-height: 1px;
				padding-right: 15px;
				padding-left: 15px;
			}

			.col-xs-5ths {
				width: 20%;
				float: left;
			}
			@media (min-width: 768px) {
				.col-sm-5ths {
					width: 20%;
					float: left;
				}
			}
			@media (min-width: 992px) {
				.col-md-5ths {
					width: 20%;
					float: left;
				}
			}
			@media (min-width: 1200px) {
				.col-lg-5ths {
					width: 20%;
					float: left;
				}
			}

			.costili:nth-child(1){
				padding-right: 40px;
			}
			.costili:last-child{
				padding-left: 40px;
			}

			.costili:nth-child(2){
				padding-left: 20px;
			}
			.costili:nth-child(3){
				padding-right: 20px;
			}

		</style>

		<div class="row">



			<div class="
			col-lg-10 col-lg-offset-1
			col-xs-12 col-xs-offset-0 blur">
				<div style="padding: 20px 0 10px 0;">
				<!-- Nav tabs -->
				<ul class="collection-title nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a id="tab-collection" href="#collection" aria-controls="collection" role="tab" data-toggle="tab"><?php echo $lang['profile']['my_collection']; ?></a></li>
					/
					<li role="presentation"><a id="tab-trophies" href="#trophies" rel="nofollow noopener" aria-controls="trophies" role="tab" data-toggle="tab"><?php echo $lang['profile']['all_collection']; ?></a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="collection">

						<div class="collection-list">

							<div class="row">
								<div class="owl-carousel owl-theme">
								<?php foreach($achievements as $key => $achievement): ?>
									<?php if($achievement->unlock): ?>
									<div class="achievement-icon" title="<?php echo e($lang['achievements'][$achievement->name]['desc']); ?>">
										<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
										<h3><?php echo e($achievement->name); ?></h3>
										<p class="achievement-desc"></p>
										<!-- <p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p> -->
									</div>
									<?php else: ?>
									<div class="achievement-icon" title="<?php echo e($lang['achievements'][$achievement->name]['desc']); ?>" style="opacity: 0;">
										<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
										<h3><?php echo e($achievement->name); ?></h3>
										<p class="achievement-desc"></p>
										<!-- <p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p> -->
									</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
							</div>

						</div>

					</div>
					<div role="tabpanel" class="tab-pane" id="trophies">
						<div class="collection-list five-in-row">
							<h2><?php echo $lang['profile']['lvl_collection']; ?></h2>

							<?php  
								$i = 0; 
								$lvlCount = 5;
							 ?>
							<div>
							<?php foreach($achievements as $key => $achievement): ?>
								<?php if($achievement->category == "lvl" && $i < $lvlCount): ?>
									<?php if($achievement->unlock): ?>
										<div class="col-md-5ths achievement-icon">
											<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
											<h3><?php echo e($achievement->name); ?></h3>
											<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
											<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
										</div>
										<?php else: ?>
										<div class="col-md-5ths achievement-icon" style="opacity: 0.5;">
											<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
											<h3><?php echo e($achievement->name); ?></h3>
											<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
											<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
										</div>
									<?php endif; ?>
									<?php 
										$i++;
									 ?>
								<?php endif; ?>
							<?php endforeach; ?>
							</div>

						<div class="row" style="padding-left: 115px; padding-right: 115px;">
							<?php 
								$i = 0;
								$lvlCount = 4;
							 ?>
							<?php foreach($achievements as $key => $achievement): ?>
								<?php if($achievement->category == "lvl" && $i < $lvlCount && $key > 4): ?>
									<?php if($achievement->unlock): ?>
										<div class="col-md-3 achievement-icon costili">
											<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
											<h3><?php echo e($achievement->name); ?></h3>
											<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
											<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
										</div>
										<?php else: ?>
										<div class="col-md-3 achievement-icon costili" style="opacity: 0.5;">
											<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png">
											<h3><?php echo e($achievement->name); ?></h3>
											<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
											<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
										</div>
									<?php endif; ?>
									<?php 
										$i++;
									 ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
						</div>

						<div class="collection-list collection-list-left">
						<div class="row">

							<div class="col-sm-6">
								<h2><?php echo $lang['profile']['gen_collection']; ?></h2>

								<div style="max-height: 360px; overflow-y: scroll; overflow-x: hidden;">
								<div class="row">
								<?php foreach($achievements as $key => $achievement): ?>
									<?php if($achievement->category == "other"): ?>
										<?php if($achievement->unlock): ?>
											<div class="col-sm-12 achievement-icon">
												<div class="row">
													<div class="col-sm-3">
												<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
													</div>
													<div class="col-sm-5" style="margin-top: 22px;">
												<h3><?php echo e($achievement->name); ?></h3>
												<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
													</div>
													<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
												</div>
												</div>
											</div>
											<?php else: ?>
											<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
												<div class="row">
													<div class="col-sm-3">
												<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
													</div>
													<div class="col-sm-5" style="margin-top: 22px;">
												<h3><?php echo e($achievement->name); ?></h3>
												<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
													</div>
													<div class="col-sm-4" style="margin-top: 35px;">
												<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
											</div>
											</div>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?>
								</div>
								</div>

							</div>
							<div class="col-sm-6">

								<div>

									<!-- Nav tabs -->
									<ul class="nav nav-tabs rsp-center" role="tablist">
										<li role="presentation" class="active rsp-link-active rsp-link"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?php echo $lang['profile']['r']; ?></a></li>
										<li role="presentation" class="rsp-link"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $lang['profile']['s']; ?></a></li>
										<li role="presentation" class="rsp-link"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><?php echo $lang['profile']['p']; ?></a></li>
									</ul>

									<div style="max-height: 360px; overflow-y: scroll; overflow-x: hidden;">

									<!-- Tab panes -->
									<div class="tab-content">
										<div role="tabpanel" class="tab-pane active" id="home">
										<?php foreach($achievements as $key => $achievement): ?>
											<?php if($achievement->category == "double"): ?>
												<?php if($achievement->unlock): ?>
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
														<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													<?php else: ?>
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
										</div>
										<div role="tabpanel" class="tab-pane" id="profile">
										<?php foreach($achievements as $key => $achievement): ?>
											<?php if($achievement->category == "bandit"): ?>
												<?php if($achievement->unlock): ?>
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
																<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													<?php else: ?>
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
										</div>
										<div role="tabpanel" class="tab-pane" id="messages">
										<?php foreach($achievements as $key => $achievement): ?>
											<?php if($achievement->category == "poker"): ?>
												<?php if($achievement->unlock): ?>
													<div class="col-sm-12 achievement-icon">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
													<?php else: ?>
													<div class="col-sm-12 achievement-icon" style="opacity: 0.5;">
														<div class="row">
															<div class="col-sm-3">
																<img src="/assets/images/prizes/<?php echo e($achievement->img); ?>.png" class="col-sm-4">
															</div>
															<div class="col-sm-5" style="margin-top: 22px;">
														<h3><?php echo e($achievement->name); ?></h3>
														<p class="achievement-desc"><?php echo e($lang['achievements'][$achievement->name]['desc']); ?></p>
															</div>
															<div class="col-sm-4" style="margin-top: 35px;">
														<p class="achievement-price"><?php echo e($lang['achievements'][$achievement->name]['value']); ?><span class="coin noselect"></span></p>
													</div>
													</div>
													</div>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
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
					<span><?php echo $lang['profile']['free']; ?></span><br>
					<small><?php echo $lang['profile']['enter']; ?></small>

					<form class="form-inline">
					 	<div class="col-sm-12">
							<input type="text" id="promoCode">
						</div>
						<button type="button" class="myButton" id="promoButton"><?php echo $lang['profile']['btn']; ?></button>
					</form>

					<br>
					<small>You referral code : <?php echo e($u->ref); ?></small>
				</div>

</div>
		</div>




	</div>
<audio autoplay loop id="myAudio" preload="auto">
				  <source src="<?php echo e(asset('assets/frontend/sounds/tech-background.mp3')); ?>" type="audio/mpeg">
				Your browser does not support the audio element.
				</audio>

	<script src="<?php echo e(asset('assets/frontend/scripts/bootstrap.min.js')); ?>"></script>

	<script src="<?php echo e(asset('assets/frontend/scripts/main.js')); ?>"> </script>
	<script src="<?php echo e(asset('assets/js/owl.carousel.min.js')); ?>"> </script>

	<link href="<?php echo e(asset('assets/css/toastr.css')); ?>" rel="stylesheet">
    <script src="<?php echo e(asset('assets/js/toastr.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/socket.io-1.3.5.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>

<div style="display: none;">
<?php echo e($language = session()->get('language', 'en')); ?>

<?php if($language == 'en'): ?> 
<?php echo e($message = $config->alert_message_en); ?>

<?php else: ?>
<?php echo e($message = $config->alert_message_ru); ?>

<?php endif; ?>  
</div>

<?php if($config->alert_active): ?>
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


    var type = '<?php echo e($config->alert_type); ?>';
    switch(type)
    {
      case 'success' :
        toastr.success('<?php echo e($message); ?>');
        break;
      case 'error' :
        toastr.error('<?php echo e($message); ?>');
        break;
      case 'warning' :
        toastr.warning('<?php echo e($message); ?>');
        break;
      case 'info' :
        toastr.info('<?php echo e($message); ?>');
        break;
    }
  });
</script>
<?php endif; ?>
</body>
</html>
