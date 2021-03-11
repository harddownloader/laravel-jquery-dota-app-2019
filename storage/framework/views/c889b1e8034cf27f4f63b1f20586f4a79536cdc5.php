<!DOCTYPE html>
<html lang="en" class="poker-html">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo e($title); ?> - <?php echo e($config->sitename); ?></title>
	<?php /*<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/frontend/styles/preload.css')); ?>"/>*/ ?>


	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fonts.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bootstrap.min.css')); ?>">

	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/style.css')); ?>">


	<link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/poker.css')); ?>">
	<script src="<?php echo e(asset('assets/frontend/scripts/jquery-3.2.0.min.js')); ?>" charset="utf-8"></script>
	<script src="<?php echo e(asset('assets/js/jquery.session.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/jquery.cookie.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/bootstrap.min.js')); ?>" charset="utf-8"></script>


    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fog.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/pers.css')); ?>">

	<script src="<?php echo e(asset('assets/js/socket.io-1.3.5.js')); ?>"></script>

	<script>
		$(document).ready(function() {
			$('#myModalBet').on('shown.bs.modal', function() {
			    $(document).off('focusin.modal');
			});
		});
	</script>

	<script type="text/javascript">
		var PutVariable = 'ante';
		const LOSE_MSG = "<?php echo e($lang['poker']['lose']); ?>";
		const WIN_MSG = "<?php echo e($lang['poker']['win']); ?>";
		const NGAME_MSG = "<?php echo e($lang['poker']['ngame']); ?>";
		const IDENTITY_MSG = "<?php echo e($lang['poker']['idcombo']); ?>";
	</script>

	<?php if(Auth::check()): ?>
		<script type="text/javascript">
			const USER_ID = parseFloat('<?php echo e($u->id); ?>');
			const USER_USERNAME = '<?php echo e($u->username); ?>';
			const USER_STEAMID64 = '<?php echo e($u->steamid64); ?>';
			const USER_AVATAR = '<?php echo e($u->avatar); ?>';
            const BET_TIMER = parseFloat('<?php echo e($config->poker_bet_timer); ?>');
            const RAISE_TIMER = parseFloat('<?php echo e($config->poker_raise_timer); ?>');
		</script>
	<?php endif; ?>

	<?php if(!is_null($game)): ?>
		<script type="text/javascript">
			const GAME_STATUS = '<?php echo e($game->status); ?>';
			const ANTE = parseFloat('<?php echo e($game->ante); ?>');
			const BLIND = parseFloat('<?php echo e($game->blind); ?>');
			const TRIPS = parseFloat('<?php echo e($game->trips); ?>');
			const BET = parseFloat('<?php echo e($game->bet); ?>');
			var CARD_1 = ('<?php echo e(json_encode($userCards)); ?>').replace(/&quot;/g, '');
		</script>
	<?php else: ?>
		<script type="text/javascript">
			const GAME_STATUS = 0;
			const ANTE = 0;
			const BLIND = 0;
			const TRIPS = 0;
			const BET = 0;
			const CARD_1 = [];
			const CARD_2 = [];
		</script>
	<?php endif; ?>

	<?php if(Auth::check()): ?>
		<script src="<?php echo e(asset('assets/js/online.js')); ?>"></script>
	<?php endif; ?>

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


<?php /*<div class="preload " id="preload-simple">*/ ?>
  <?php /*<div class="col-xs-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 my-font-regular"*/ ?>
  <?php /*id="preload-text">*/ ?>
  <?php /*<img src="<?php echo e(asset('assets/images/loader.gif')); ?>"*/ ?>
  <?php /*class="img-responsove vcenter" style="width: 100px;">*/ ?>
  <?php /*</div>*/ ?>
<?php /*</div>*/ ?>
<?php /*<script src="<?php echo e(asset('assets/frontend/scripts/preload.js')); ?>"></script>*/ ?>
<?php /*<script>simplePreload();</script>*/ ?>

<canvas id="myCanvasFog"></canvas>
<div id="back"></div>

<div class="modal custom fade my-font-regular t-spacing hcenter"
 id="myModalBet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
        <span class="modal-title my-font" id="myModalLabel"><?php echo $lang['poker']['place']; ?></h4>
      </div>
      <div class="modal-body row">

		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space" style="margin-left: 20px; margin-right:-20px;">
		  	<button type="button" class="btn btn-xs btn-poker btn-undo btn-paytable" onclick="$('#myModalTripsBlinds').modal('show');"><?php echo $lang['poker']['paytable']; ?></button>
		  	<button type="button" class="btn btn-xs btn-poker btn-undo btn-paytable" onclick="$('#myModalRules').modal('show');"><?php echo $lang['poker']['rules']; ?></button>
		  </div>

		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space">
		  <span><?php echo $lang['poker']['bet']; ?> </span><span id="bet">0</span>
		  <div class="coin noselect"></div>
		  </div>
		  <div class="col-xs-4 pad hcenter my-font-regular bottom-space">
		  <button type="button" class="btn btn-xs btn-poker btn-undo" style="margin-left: -60px;"><?php echo $lang['poker']['undo']; ?></button>
		  <button type="button" class="btn btn-xs btn-poker btn-clear"><?php echo $lang['poker']['clear']; ?></button>
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
            <span id="bet-timer"><?php echo e($config->poker_bet_timer); ?></span>
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
        <span class="modal-title my-font" id="myModalLabel"><?php echo $lang['poker']['take_dec']; ?></h4>
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
            <span id="check-timer"><?php echo e($config->poker_raise_timer); ?></span>
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
        <span class="modal-title my-font" id="myModalLabel"><?php echo $lang['poker']['accepted']; ?></h4>
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
<?php echo $lang['poker']['rules_text']; ?>





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
    <span class="modal-title my-font" id="myModalLabel"><?php echo $lang['poker']['blind']; ?></span>
<br><br>
<div>
<ul style="padding : 0; text-align:left !important;" class="pay-card-table">
	<li><img src="<?php echo e(asset('assets/images/new-cards/10.png')); ?>"> - 500x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/9.png')); ?>"> - 50x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/8.png')); ?>"> - 10x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/7.png')); ?>"> - 3x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/6.png')); ?>"> - 1.5x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/5.png')); ?>"> - 1x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/4.png')); ?>"> - 0x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/3.png')); ?>"> - 0x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/2.png')); ?>"> - 0x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/1.png')); ?>"> - 0x</li>
</ul>
</div>
</div>
    <div class="col-xs-5">
        <span class="modal-title my-font" id="myModalLabel"><?php echo $lang['poker']['trips']; ?></span>
        <br><br>
<div>
<ul style="padding : 0; text-align:left !important;" class="pay-card-table">
	<li><img src="<?php echo e(asset('assets/images/new-cards/10.png')); ?>"> - 100x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/9.png')); ?>"> - 40x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/8.png')); ?>"> - 20x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/7.png')); ?>"> - 7x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/6.png')); ?>"> - 6x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/5.png')); ?>"> - 5x</li>
	<li><img src="<?php echo e(asset('assets/images/new-cards/4.png')); ?>"> - 3x</li>
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
             <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/frontend/images/min-logo.png')); ?>" alt="" style="width: 100%"></a>
             </div>
        </div>

			</div>





</div>
    <div id="poker-line" class="layer my-font-regular"  data-depth="0.1">
    <div class="p0 mycoins t-spacing" id="coins-block" >
        <div class="col-xs-12 p0">
            <span><?php echo $lang['poker']['mycoins']; ?></span>
        </div>
        <div class="col-xs-12 p0" >
            <span id="mycoins"><?php echo e(number_format($u->money, 0, ' ', ' ')); ?></span><div class="coin noselect"></div>
        </div>
    </div>

    <div class="p0 mycoins t-spacing" id="total-block" >
        <div class="col-xs-12 p0">
            <span><?php echo $lang['poker']['total']; ?></span>
        </div>
        <div class="col-xs-12 p0" >
            <span id="totalcoins"><?php if(is_null($game)): ?> 0 <?php else: ?> <?php echo e($game->total); ?> <?php endif; ?></span><div class="coin noselect"></div>
        </div>
    </div>
	<div id="player-cards-block" style="<?php if(is_null($game)): ?> display : none; <?php endif; ?>">
        <span class="t-spacing"><?php echo $lang['poker']['player']; ?></span>
        <span class="t-spacing" id=user_win style="display:none;"><?php echo $lang['poker']['uwin']; ?></span>
        <!-- <span class="t-spacing yellow-text">ANTE x1</span>
        <span class="t-spacing yellow-text">PLAY x1</span> -->
        <div id="player-cards" class="p0">
			<?php if(!is_null($game)): ?>
				<?php if($game->status == 1): ?>
					<?php foreach($combo1['used'] as $card): ?>
						<?php if(isset($card['used'])): ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						<?php else: ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php for($i = count($combo1['used']); $i < 5; $i++): ?>
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					<?php endfor; ?>
				<?php elseif($game->status == 2): ?>
					<?php foreach($combo2['used'] as $card): ?>
						<?php if(isset($card['used'])): ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						<?php else: ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php for($i = count($combo2['used']); $i < 5; $i++): ?>
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					<?php endfor; ?>
				<?php elseif($game->status == 3): ?>
					<?php foreach($combo3['used'] as $card): ?>
						<?php if(isset($card['used'])): ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>
						<?php else: ?>
							<div class="card" style="background : url(./assets/images/cards/<?php echo e($card['id']); ?>_<?php echo e($card['section']); ?>.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php for($i = count($combo3['used']); $i < 5; $i++): ?>
						<div class="card" style="background : url(./assets/images/cards/2_2.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0;"></div>
					<?php endfor; ?>
				<?php endif; ?>
			<?php endif; ?>
        </div>
		<?php if(!is_null($game)): ?>
			<?php if($game->status == 1): ?>
			<span class="t-spacing" id="user-combo"><?php echo e($combo1['name']); ?></span>
			<?php elseif($game->status == 2): ?>
			<span class="t-spacing" id="user-combo"><?php echo e($combo2['name']); ?></span>
			<?php elseif($game->status == 3): ?>
			<span class="t-spacing" id="user-combo"><?php echo e($combo3['name']); ?></span>
			<?php endif; ?>
		<?php else: ?>
			<span class="t-spacing" id="user-combo" style="display : none;">NONE</span>
		<?php endif; ?>
        <span class="t-spacing yellow-text" id="user-status" style="display : none;"><?php echo $lang['poker']['check']; ?></span>
    </div>
    <div id="dealer-cards-block" style="display : none;">
        <span class="t-spacing"><?php echo $lang['poker']['dealer']; ?></span>
		<span class="t-spacing" id=diler_win style="display:none;"><?php echo $lang['poker']['dwin']; ?></span>
        <div id="player-cards" class="p0">
        </div>
        <span class="t-spacing yellow-text" id="dealer-combo" style="display:none;">NONE</span>
    </div>

    <div id="field">
        <div id="field-enemy-cards" class="filed-row p0" style="width: 40%; margin-left: 30%; ">
			<?php if(!is_null($game)): ?>
				<div class="card" style="background : url(./assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
				<div class="card" style="background : url(./assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
			<?php endif; ?>
        </div>
        <div id="game-cards" class="filed-row p0">
			<?php if(!is_null($game)): ?>
				<?php if($game->status == 2): ?>
					<?php for($i = 0; $i < 3; $i++): ?>
						<div class="card" style="background : url(./assets/images/cards/<?php echo e($game->cards[2][$i]->id); ?>_<?php echo e($game->cards[2][$i]->section); ?>.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
					<?php endfor; ?>
				<?php elseif($game->status == 3): ?>
					<?php for($i = 0; $i < 5; $i++): ?>
						<div class="card" style="background : url(./assets/images/cards/<?php echo e($game->cards[2][$i]->id); ?>_<?php echo e($game->cards[2][$i]->section); ?>.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
					<?php endfor; ?>
				<?php endif; ?>
			<?php endif; ?>
        </div>
        <div id="my-cards" class="filed-row p0" style="width: 40%; margin-left: 30%; margin-top: 7%">
			<?php if(!is_null($game)): ?>
				<?php foreach($game->cards[0] as $card): ?>
					<div class="card" style="background : url(./assets/images/cards/<?php echo e($card->id); ?>_<?php echo e($card->section); ?>.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>
				<?php endforeach; ?>
			<?php endif; ?>
        </div>
        <div class="anim-ball"></div>
    </div>

    <a class="myButton diamond-btn blue poker-play" id="play-btn" style="bottom: 3% !important;" ><?php echo $lang['poker']['play']; ?></a>


    <a class="myButton diamond-btn poker-trips not_active" id="play-btn" style="bottom: 25%; left: 40%; z-index : 1001;"><?php echo $lang['poker']['trips']; ?></a>
    <a class="myButton diamond-btn poker-ante blue" id="play-btn" style="bottom: 25%; z-index : 1001;"><?php echo $lang['poker']['ante']; ?></a>
    <a class="myButton diamond-btn poker-blind" id="play-btn" style="bottom: 25%; left: 60%; z-index : 1001;"><?php echo $lang['poker']['blind']; ?></a>
    <!-- <a class="myButton diamond-btn not-active blue" id="play-btn" style="bottom: 25%; left: 60%;" data-toggle="modal" data-target="#myModalChoose">BLIND</a> -->

    <!-- <a class="myButton diamond-btn" id="play-btn" style="bottom: 25%"><div class="chip-img chip-25"></div>&nbsp;</a> -->
    <!-- <a class="myButton diamond-btn blue" id="play-btn" style="bottom: 25%; left: 60%;"><div class="chip-img chip-100"></div>&nbsp;</a> -->


    </div>

	<!--<img src="images/poker/pers.png"  class="layer"  id="totem" data-depth="0.05" style="bottom: 0 !important;">-->

</div>
</div>


    <audio autoplay loop id="myAudio" preload="auto">
                  <source src="<?php echo e(asset('assets/frontend/sounds/poker_sound_cut.mp3')); ?>" type="audio/mpeg">
                Your browser does not support the audio element.
                </audio>

	<script src="<?php echo e(asset('assets/frontend/scripts/jquery.parallax.js')); ?>"></script>
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

	<script src="<?php echo e(asset('assets/frontend/scripts/main.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/frontend/scripts/chat.js')); ?>"></script>

 <?php /* <script src="<?php echo e(asset('assets/frontend/scripts/fog.js')); ?>"></script> */ ?>

 <script src="<?php echo e(asset('assets/frontend/scripts/pers.js')); ?>"></script>

 <link href="<?php echo e(asset('assets/css/toastr.css')); ?>" rel="stylesheet">
 <script src="<?php echo e(asset('assets/js/toastr.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/poker.js')); ?>"></script>
 <script src="<?php echo e(asset('assets/js/chat.js')); ?>"></script>

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

</body>
</html>
