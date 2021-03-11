<!DOCTYPE html>
<html lang="en" class="bandit-html">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo e($title); ?> - <?php echo e($config->sitename); ?></title>
	<?php /*<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/frontend/styles/preload.css')); ?>"/>*/ ?>


	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fonts.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bootstrap.min.css')); ?>">

	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/style.css')); ?>">


	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bandit.css')); ?>">
	<script src="<?php echo e(asset('assets/frontend/scripts/jquery-3.2.0.min.js')); ?>" charset="utf-8"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/bootstrap.min.js')); ?>" charset="utf-8"></script>
	<script src="<?php echo e(asset('assets/js/jquery.session.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/jquery.cookie.js')); ?>"></script>


		<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fog.css')); ?>">
		<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bandit-lines.css')); ?>">

		<?php if(Auth::check()): ?>
			<script type="text/javascript">
				const USER_ID = parseFloat('<?php echo e($u->id); ?>');
				const USER_USERNAME = '<?php echo e($u->username); ?>';
				const USER_STEAMID64 = '<?php echo e($u->steamid64); ?>';
				const USER_AVATAR = '<?php echo e($u->avatar); ?>';
			</script>
		<?php endif; ?>

		<script>
			// const LANG = '<?php echo e(\Session::get("language", "ru")); ?>';
			const BONUS_1 = '<?php echo e($lang["bandit"]["bonus2"]); ?>';
			const BONUS_2 = '<?php echo e($lang["bandit"]["bonus1"]); ?>';
			const BONUS_3 = '<?php echo e($lang["bandit"]["bonus3"]); ?>';
			const WIN = '<?php echo e($lang["bandit"]["win"]); ?>';
		</script>

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
  <?php /*</div>*/ ?>
<?php /*</div>*/ ?>
<?php /*<script src="<?php echo e(asset('assets/frontend/scripts/preload.js')); ?>"></script>*/ ?>
<?php /*<script>simplePreload();</script>*/ ?>

<canvas id="myCanvasFog"></canvas>
<div id="back"></div>

<div class="user-info fixed-info hideonload noselect" style="height: auto;">
        <a href="<?php echo e(route('profile')); ?>">
          <div class=" my-font-regular t-spacing" >
          <!--<div class="separator"></div>-->
            <div class="icon" style="background : url(<?php echo e($u->avatar); ?>); border-radius : 50%; background-size : 100%;"></div>
            <span><?php echo e($u->username); ?></span>
            <small>LVL</small>
            <span id="info-level"><?php echo e($u->lvl); ?></span>
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

<?php echo $__env->make('right-side', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<aside class="sliding-panel">

<div class="help-text" style="height:100%">
		<div class="col-md-12 my-font-regular style-3" id="chat" >

            <div class="row current-chat-area">
                <div class="col-md-12">
					<ul class="media-list" id="messages">
						<?php foreach($chat as $message): ?>
						  <li class="media">
							  <div class="media-body">
								  <div class="media">
									  <a class="pull-left" href="#">
										  <div class="user-img chat-img" style="background-image: url(<?php echo e($message['avatar']); ?>)"></div>
									  </a>
									  <div class="media-body">
										  <span class="chat-name" style="cursor : pointer;"><?php echo e($message['username']); ?></span>
										  <small>LVL</small><span class="chat-level"><?php echo e($message['lvl']); ?></span><br>
										  <small class="chat-message"><?php echo e($message['message']); ?></small>

									  </div>
								  </div>

							  </div>
						  </li>
					  <?php endforeach; ?>
				  </ul>
                </div>
            </div>

		</div>
        <textarea  id="chat-input" placeholder="<?php echo $lang['chat']['input']; ?>" class="my-font"></textarea>
<!--*******************<**************************************************-->
</div>
</aside>













<div id="scene" >
<div  id="wrapper" class="layer noblur" data-depth="0.10" >




	<div class="container
	col-lg-6 col-lg-offset-3
	col-md-8 col-md-offset-2
	col-xs-10 col-xs-offset-1
	bandit-container p-top25" >
	<!--col-md-8 col-md-offset-2 -->
		<div class="sep"></div>
		<div class="sep"></div>
		<div class="row">
			<div class="

			col-sm-6 col-sm-offset-3
			col-xs-8 col-xs-offset-2

			 bottom-space noselect" >
			 <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/frontend/images/min-logo.png')); ?>" alt="" style="width: 100%"></a>
			 </div>
		</div>
		<div class="col-xs-12
		   t-spacing my-font-regular hcenter
		   bottom-space
		   ">
		   <span id="bandt-title" class=""><?php echo $lang['bandit']['bonus_title']; ?></span>
		   </div>
		<div class="row bottom-space rel" style="height:100%">
			<!-- <div class="side-numbers my-font-regular noselect" style="right: 100%; margin-right: 50px;">
			<ul><li>8</li><li>2</li><li>4</li><li>6</li><li>1</li><li>7</li><li>9</li><li>5</li><li>3</li><li>10</li>
				</ul>
			</div>
			<div class="side-numbers my-font-regular noselect" style="left: 100%; margin-left: 15px;">
			<ul><li>9</li><li>2</li><li>4</li><li>10</li><li>1</li><li>6</li><li>7</li><li>5</li><li>3</li><li>8</li>
				</ul>
			</div> -->
			<div class="
			bandit-block scroll-slots0">

			<div class="bandit-menu my-font-regular t-spacing hcenter noselect">
			<div class="pos-hcenter pos-vcenter translate menu">
			<a class="bottom-space50 CONTINUE_GAME"><?php echo $lang['bandit']['continue']; ?></a>
			<a class="FINISH_GAME"><?php echo $lang['bandit']['finish']; ?></a>
			</div>

			<div class="pos-hcenter pos-vcenter translate menu3 show-start">
			<a class="btn btn-start towersStarts" data-tower="0">
			<img src="<?php echo e(asset('assets/frontend/images/slot/bonus/svg/start.svg')); ?>">
			</a>

			<a class="btn btn-continue CONTINUE_GAME">
			<img src="<?php echo e(asset('assets/frontend/images/slot/bonus/svg/continue.svg')); ?>">
			</a>
			<a class="btn btn-finish FINISH_GAME">
			<img src="<?php echo e(asset('assets/frontend/images/slot/bonus/svg/finish.svg')); ?>">
			</a>

			</div>


			</div>

			<div class="game3">
			<div class ="totem1 select-rotator active- disabled- totem_event" select-rotator="-180" data-tower="1">
			<div class="pos-hcenter pos-vcenter translate hcenter noselect">
			<span class="totem1_m totem_m" style="display:none;">2 x</span>
			</div>
			</div>
			<div class ="totem2 select-rotator active- disabled- totem_event" select-rotator="0" data-tower="2">
			<div class="pos-hcenter pos-vcenter translate hcenter noselect">
			<span class="totem2_m totem_m" style="display:none;">0 x</span>
			</div>
			</div>
			<div class="pos-hcenter pos-vcenter translate t-spacing hcenter noselect">
			<span class="hide">SELECT<br>TOWER</span>
			<div class="selector hide-"></div>
			</div>

			</div>

			<div id="bandit-lines">

				<div class="bandit-line" style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/11_51.png')); ?>); display : none;" data-line="2"></div>
				<div class="bandit-line" style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/12_52.png')); ?>); display : none;" data-line="1"></div>
				<div class="bandit-line" style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/13_53.png')); ?>); display : none;" data-line="3"></div>

				<div class="bandit-line line-zag-tobottom" style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/11_33_51.png')); ?>); display : none;" data-line="4"></div>
				<div class="bandit-line line-zag-totop" style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/13_31_53.png')); ?>); display : none;" data-line="5"></div>

				<div class="bandit-line line-totop"
				style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/12_21_41_52.png')); ?>); display : none;" data-line="9"></div>
				<div class="bandit-line line-tobottom"
				style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/12_23_43_52.png')); ?>); display : none;" data-line="8"></div>
				<!-- -->

				<div class="bandit-line line-h2"
				style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/13_23_32_43_53.png')); ?>); display : none;" data-line="7"></div>
				<div class="bandit-line line-h2"
				style="background-image: url(<?php echo e(asset('assets/frontend/images/lines/11_21_32_41_51.png')); ?>); display : none;" data-line="6"></div>
			</div>

				<div class="slot"  >
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
				</div>
				<div class="slot">
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/7.png')); ?>)"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/4.png')); ?>)"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
					<div class="slot-element"></div>
				</div>
				<div class="slot">
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/6.png')); ?>)"></div>
				</div>
				<div class="slot">
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/4.png')); ?>)"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
				</div>
				<div class="slot">
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/4.png')); ?>)"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
					<div class="slot-element"></div>
					<div class="slot-element" style="background-image: url(<?php echo e(asset('assets/frontend/images/slot/3.png')); ?>)"></div>
				</div>


			</div>
		</div>

		<div class="col-xs-12
t-spacing my-font-regular hcenter
bottom-space

bandit-text-container

">
<span class="bandit-text questGame_text" ><?php echo $lang['bandit']['bonus1']; ?></span>
<span class="bandit-text mGame_text" ><?php echo $lang['bandit']['bonus2']; ?></span>
<span class="bandit-text towers_text" ><?php echo $lang['bandit']['bonus3']; ?></span>
</div>
<div class="row bonus-row" style="font-size: 80%;">
<div class="mycoins t-spacing
col-xs-12

align-middle my-font-regular" >
<div class="col-xs-4 p0">
<div class="col-xs-12 p0">
<span><?php echo $lang['bandit']['balance']; ?></span>
</div>
<div class="col-xs-12 bottom-space">
<span id="mycoins" class="mycoins_value"><?php echo e(number_format($u->money, 0, ' ', ' ')); ?></span><div class='coin noselect'></div>
</div>
</div>
<div class="col-xs-4 p0">
<div class="col-xs-12 p0">
<span><?php echo $lang['bandit']['totalbet']; ?></span>
</div>
<div class="col-xs-12">
<span id="totalbet" class="totalbet_value">100</span><div class='coin noselect'></div>
</div>
</div>
<div class="col-xs-4 p0">
<div class="col-xs-12 ">
<span><?php echo $lang['bandit']['win']; ?></span>
</div>
<div class="col-xs-12 bottom-space">
<span id="win" class="win_value">0</span><div class='coin noselect'></div>
</div>
</div>
</div>

</div>

		<div class="row normal-row" style="font-size: 80%;" id="bottom-side">

			<div class="mycoins t-spacing
				col-xs-6 col-md-3

				align-middle my-font-regular" >
					<div class="col-xs-8 col-xs-offset-2 col-md-12 col-md-offset-0 p0">
						<div class="col-xs-12 p0">
							<span><?php echo $lang['bandit']['balance']; ?></span>
						</div>
						<div class="col-xs-12 bottom-space">
							<span id="mycoins" class="mycoins_value"><?php echo e(number_format($u->money, 0, ' ', ' ')); ?></span><div class='coin noselect'></div>
						</div>

						<a href="/bandit/paytable" id="paytableURL" class="myButton"><?php echo $lang['bandit']['paytable']; ?></a>

					</div>
			</div>
			<div class="mycoins t-spacing
				col-xs-6 col-md-4
				align-middle my-font-regular noselect" >
				<div class="row " >
						<div class="col-xs-6">
							<div class="row">
								<div class="col-xs-12">
									<span><?php echo $lang['bandit']['bet']; ?></span>
								</div>
							</div>
							<div class="row p0 bottom-space">
								<span class="rel"><span id="bet">100</span>
									<div class="bandit-btn btn-pos-left btn-bet" data-type="minus">-</div>
									<div class="bandit-btn btn-pos-right btn-bet" data-type="plus">+</div>
								</span>
							</div>
						</div>
						<div class="col-xs-6 ">
							<a  class="myButton" id="btn-maxbet"><?php echo $lang['bandit']['maxbetbtn']; ?></a>
						</div>
				</div>
				<div class="row " >
						<div class="col-xs-6">
							<div class="row">
								<div class="col-xs-12">
									<span><?php echo $lang['bandit']['line']; ?></span>
								</div>
							</div>
							<div class="row p0">
								<span class="rel"><span id="line" class="line_value">1</span>
									<div class="bandit-btn btn-pos-left btn-line" data-type="minus">-</div>
									<div class="bandit-btn btn-pos-right btn-line" data-type="plus">+</div>
								</span>
							</div>
						</div>
						<div class="col-xs-6 ">
							<a class="myButton p0" id="btn-autoplay" data-enabled="0"><?php echo $lang['bandit']['autoplay']; ?></a>
						</div>
				</div>

			</div>
			<div class="mycoins t-spacing height-initial
				col-xs-12 col-md-3
				align-middle my-font-regular" >

					<div class="col-xs-6 col-md-12">
						<div class="col-xs-12 ">
							<span><?php echo $lang['bandit']['win']; ?></span>
						</div>
						<div class="col-xs-12 bottom-space">
							<span id="win" class="win_value">0</span><div class='coin noselect'></div>
						</div>
					</div>
					<div class="col-xs-6 col-md-12 p0">
						<div class="col-xs-12 p0">
							<span><?php echo $lang['bandit']['totalbet']; ?></span>
						</div>
						<div class="col-xs-12">
							<span id="totalbet" class="totalbet_value">100</span><div class='coin noselect'></div>
						</div>
					</div>



			</div>
			<div class="p0
		col-md-2 col-md-offset-0
		col-xs-10 col-xs-offset-1 ">
			<a  class="myButton" id="btn-spin" ><br><?php echo $lang['bandit']['spin']; ?></a>
		</div>



		</div>
	</div>


</div>

	<img src="<?php echo e(asset('assets/frontend/images/totem-bandit.png')); ?>"  class="layer"  id="totem" data-depth="0.02" style="bottom: 0 !important;">

</div>



    <audio autoplay loop id="myAudio" preload="auto">
                  <source src="<?php echo e(asset('assets/frontend/sounds/bandit-environment.mp3')); ?>" type="audio/mpeg">
                Your browser does not support the audio element.
                </audio>

	<script src="<?php echo e(asset('assets/frontend/scripts/jquery.parallax.js')); ?>"></script>
		<script>
					$('#scene').parallax();
	</script>

	<script src="<?php echo e(asset('assets/frontend/scripts/main.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/chat.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/bonus.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/content-chat-move.js')); ?>"></script>

 <script src="<?php echo e(asset('assets/frontend/scripts/fog.js')); ?>"></script>
 <link href="<?php echo e(asset('assets/css/toastr.css')); ?>" rel="stylesheet">
 <script src="<?php echo e(asset('assets/js/toastr.js')); ?>"></script>

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

 <script src="<?php echo e(asset('assets/js/socket.io-1.3.5.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/bandit.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/chat.js')); ?>"></script>

</body>
</html>
