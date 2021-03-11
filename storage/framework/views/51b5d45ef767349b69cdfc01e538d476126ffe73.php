<!DOCTYPE html>
<html lang="en" class="profile-html">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache" />
    <title><?php echo e($title); ?> - <?php echo e($config->sitename); ?></title>
    <script src="<?php echo e(asset('assets/frontend/scripts/jquery-3.2.0.min.js')); ?>"></script>
    <?php /*<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/frontend/styles/preload.css')); ?>"/>*/ ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/fonts.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/styles/bootstrap.min.css')); ?>">

    <style>

        html, body, #my-video{
            width: 100%;
            height: 100%;
            background: #000;
        }

.video-panel{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    -webkit-box-shadow: inset 0px 0px 500px 0px rgba(0,0,0,1);
    -moz-box-shadow: inset 0px 0px 500px 0px rgba(0,0,0,1);
    box-shadow: inset 0px 0px 500px 0px rgba(0,0,0,1);
    z-index: 5;
}

        .video-js .vjs-big-play-button{
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%);
        }

        .video-logo{
            position: absolute;
            left: 50%;
            top: 5%;
            transform: translateX(-50%);
            z-index: 99999;
            width: 250px;
        }

        .video-logo img{
            width: 100%;
        }

        .after-video-body{
            background: rgba(0, 0, 0, 0.9);
            position: fixed;
            top:0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99997;
            display: none;
        }

        .nav-play{
            position: absolute;
            top:50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .nav-play img{
            margin: 0 15px;
        }

        .nav-play img{
            opacity: 0.7;
        }

        .nav-play img:hover{
            opacity: 1;
            text-decoration: none;
        }

        .repeat-btn{
            position: absolute;
            bottom: 45px;
            left: 50%;
            transform: translateX(-50%);
        }

        .repeat-btn:hover{
            cursor: pointer;
        }

        .after-about-body{
            background: rgba(0, 0, 0, 0.9);
            position: fixed;
            top:0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99998;
            display: none;
        }

        .after-about-body p{
            font-family: Open Sans, Helvetica, Arial, Tahoma, sans-serif;
            letter-spacing: 0.1em;
            color: #c3bcbb;
            font-style: normal;
            font-weight: 300;
            position: absolute;
            width: 42%;
            left: 29%;
            top:50%;
            transform: translateY(-50%);
            text-align: center;
        }

        .after-about-body a{
            display: block;
            padding: 10px 25px;
            text-align: center;
            width: 20%;
            left: 40%;
            color: #c3bcbb;
            border: 1px solid #c3bcbb;
            position: absolute;
            bottom: 10%;
            text-transform: uppercase;
        }

        .show-video{
            position: absolute;
            bottom: 25px;
            left: 50px;
            color: #c3bcbb;
            text-transform: uppercase;
        }

        .show-video:hover{
            cursor: pointer;
        }

        .skip-video{
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            font-family: Open Sans, Helvetica, Arial, Tahoma, sans-serif;
            letter-spacing: 0.1em;
            color: #c3bcbb;
            font-style: normal;
            font-weight: 300;
            text-align: center;
            z-index: 99998;
        }

        .skip-video:hover{
            cursor: pointer;
        }

    </style>

    <link href="https://vjs.zencdn.net/5.8.8/video-js.css" rel="stylesheet">

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


<?php /*<div class="video-panel">*/ ?>
    <?php /*1*/ ?>
<?php /*</div>*/ ?>

<a class="video-logo" href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/frontend/images/min-logo.png')); ?>"></a>
<div class="skip-video">Skip video</div>

<div class="after-video-body">
    <div class="nav-play">
        <a href="<?php echo e(route('home')); ?>">
            <img src="<?php echo e(asset('assets/images/video/start_video_btn.png')); ?>">
        </a>
        <a href="#" class="show-about">
            <img src="<?php echo e(asset('assets/images/video/about_video_btn.png')); ?>">
        </a>
    </div>
    <div class="repeat-btn"><img src="<?php echo e(asset('assets/images/video/repeat_video_btn.png')); ?>"></div>
</div>

<div class="after-about-body">
    <p>
        <?php echo $lang['tutorial']; ?>

    </p>
    <a href="<?php echo e(route('home')); ?>">Start game</a>
    <div class="show-video">Show video</div>
</div>

<video id="my-video" class="video-js" controls autoplay preload="auto" width="1000" height="1000"
       poster="https://zero-k.info/img/clans/DOTA_bg.png" data-setup="{}">
    <source src="<?php echo e(asset('assets/dota_video.mp4')); ?>" type='video/mp4'>
    <source src="<?php echo e(asset('assets/dota_video.mp4')); ?>" type='video/webm'>
    <p class="vjs-no-js">
        To view this video please enable JavaScript, and consider upgrading to a web browser that
        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
</video>


<script src="<?php echo e(asset('assets/js/jquery.session.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/jquery.cookie.js')); ?>"></script>

<script src="<?php echo e(asset('assets/frontend/scripts/bootstrap.min.js')); ?>"></script>

<script src="https://vjs.zencdn.net/5.8.8/video.js"></script>
<script>
    $(document).ready(function() {

        var video = videojs('my-video').ready(function(){
            var player = this;

            player.on('ended', function() {
                $('.skip-video').fadeOut(100);
                $('.after-video-body').fadeIn(1000);
            });

            $('.repeat-btn').click(function () {
                $('.after-video-body').fadeOut(500);
                $('.skip-video').fadeIn(500);
                player.play();
            });

            $('.show-about').click(function () {
                $('.after-about-body').fadeIn(1000);
            });

            $('.show-video').click(function () {
                $('.after-about-body').fadeOut(500);
                $('.after-video-body').fadeOut(500);
                player.play();
            });

            $('.skip-video').click(function () {
                $(this).fadeOut(500);
                player.pause();
                $('.after-video-body').fadeIn(1000);
            });
        });

    });
</script>

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

<?php /*<script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>*/ ?>
</body>
</html>
