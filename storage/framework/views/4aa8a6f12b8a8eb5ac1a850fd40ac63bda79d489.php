<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($title); ?> - Панель управления</title>

	<!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="http://placehold.it/64.png/000/fff">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">

	<!-- Styles -->
    <link href="<?php echo e(asset('assets/css/lib/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/lib/themify-icons.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/lib/mmc-chat.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/lib/sidebar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/lib/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/lib/unix.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/toastr.css')); ?>" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="<?php echo e(asset('assets/js/toastr.js')); ?>"></script>
    <script src="https://unpkg.com/vue"></script>

    <style>

        .disable-light{
            position: absolute;
            right: 160px;
            top:13px;
            background: #fff;
            border-radius: 3px;
            border: none;
            color: #8fc9fb;
            font-weight: bold;
        }

    </style>

    <?php if($config->is_dark_theme == 1): ?>
        <link href="<?php echo e(asset('assets/css/dark-admin.css')); ?>" rel="stylesheet">
    <?php endif; ?>

</head>

<body>

    <div class="header">
        <div class="pull-left">
            <div class="logo"><img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="" /><span>Панель управления</span></div>
            <div class="hamburger sidebar-toggle">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </div>

        <div class="pull-right p-r-15">
            <button class="disable-light" data-is-dark-theme="<?php echo e($config->is_dark_theme == 0 ? '1' : '0'); ?>"><?php echo e($config->is_dark_theme == 0 ? 'Выключить свет' : 'Включить свет'); ?></button>
            <ul>
                <li class="header-icon dib"><img class="avatar-img" src="<?php echo e($u->avatar); ?>" alt="" /> <span class="user-avatar"><?php echo e($u->username); ?> <i class="ti-angle-down f-s-10"></i></span>
                    <div class="drop-down dropdown-profile">
                        <div class="dropdown-content-body">
                            <ul>
                                <li><a href="/"><i class="ti-power-off"></i> <span>Вернуться на сайт</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
        <div class="nano">
            <div class="nano-content">
                <ul>
                    <li class="active"><a href="/admin"><i class="ti-stats-up"></i> Статистика </a></li>
                    <!-- <li><a href="/admin/antiminus"><i class="ti-wand"></i> Антиминус </a></li> -->
                    <li><a href="/admin/bots"><i class="ti-harddrives"></i> Управление ботами</a></li>
                    <!-- <li><a href="/admin/proxy"><i class="ti-world"></i> Прокси</a></li> -->
                    <li><a href="/admin/promo"><i class="ti-gift"></i> Промокоды</a></li>
					<li><a href="/admin/users"><i class="ti-user"></i> Пользователи</a></li>
                    <li><a href="/admin/settings"><i class="ti-settings"></i> Настройки сайта</a></li>
                    <li><a href="/admin/chat"><i class="ti-settings"></i> Настройки чата</a></li>
                    <li><a href="/admin/shop_settings"><i class="ti-settings"></i> Настройки магазина</a></li>
					<li><a class="sidebar-sub-toggle"><i class="ti-settings"></i> Настройки игр <span class="sidebar-collapse-icon ti-angle-down"></span></a>
                        <ul>
                            <li><a href="/admin/double">Дабл</a></li>
                            <li><a href="/admin/bandit">Однорукий бандит</a></li>
                            <li><a href="/admin/poker">Покер с диллером</a></li>
                        </ul>
                    </li>
                    <li><a href="/admin/items"><i class="ti-money"></i> Предметы</a></li>
                </ul>
            </div>
        </div>
    </div><!-- /# sidebar -->

  <div class="content-wrap">
        <div class="main">
            <?php echo $__env->yieldContent('content'); ?>
        </div><!-- /# main -->
    </div><!-- /# content wrap -->



    <script src="<?php echo e(asset('assets/js/lib/jquery.min.js')); ?>"></script><!-- jquery vendor -->
    <script src="<?php echo e(asset('assets/js/lib/jquery.nanoscroller.min.js')); ?>"></script><!-- nano scroller -->
    <script src="<?php echo e(asset('assets/js/lib/sidebar.js')); ?>"></script><!-- sidebar -->
    <script src="<?php echo e(asset('assets/js/lib/bootstrap.min.js')); ?>"></script><!-- bootstrap -->

	<!--  Datamap -->
	<script src="<?php echo e(asset('assets/js/lib/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/lib/datamap/d3.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/lib/datamap/topojson.js')); ?>"></script>
	<!-- // Datamap -->
    <script src="<?php echo e(asset('assets/js/scripts.js')); ?>"></script><!-- scripit init-->

    <script>

        $(document).ready(function () {

            $('.disable-light').click(function () {

                $.ajax({
                    type: 'PATCH',
                    url: "/admin/settings/is_dark_theme/" + $('.disable-light').attr('data-is-dark-theme'),
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {

                    },
                    complete: function (data) {
                        location.reload();
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    dataType: "json"
                });

            });
            
        });

    </script>
</body>

</html>
