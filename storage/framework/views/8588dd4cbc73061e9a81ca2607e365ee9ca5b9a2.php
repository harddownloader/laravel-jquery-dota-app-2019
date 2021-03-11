

<?php $__env->startSection('content'); ?>
            <div class="container-fluid">
                <!-- <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Управление ботами</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Управление ботами</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="main-content">
                    <!-- <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
								<div class="card-header">
									<h4>Включение / Выключение ботов</h4>
								</div>
								<div class="card-body">
                                    <div class="basic-elements">
                                        <div class="row">
                                            <div class="btn btn-success btn-flat btn-addon btn-lg m-l-5 bot_online" data-type="1" data-bot-type="1"><i class="ti-check"></i>Включить ботов</div>
                                            <div class="btn btn-danger btn-flat btn-addon btn-lg m-l-5 bot_online" data-type="0" data-bot-type="1"><i class="ti-check"></i>Выключить ботов</div>
                                        </div>
                                    </div>
								</div>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
								<div class="card-header">
									<h4>Включение / Выключение сайта</h4>
								</div>
								<div class="card-body">
                                    <div class="basic-elements">
                                        <div class="row">
                                            <div class="btn btn-success btn-flat btn-addon btn-lg m-l-5 bot_online" data-type="1" data-bot-type="0"><i class="ti-check"></i>Включить сайт</div>
                                            <div class="btn btn-danger btn-flat btn-addon btn-lg m-l-5 bot_online" data-type="0" data-bot-type="0"><i class="ti-check"></i>Выключить сайт</div>
                                        </div>
                                    </div>
								</div>
                            </div>
                        </div>
                    </div> -->
					<div class="row">
						<div class="col-lg-12">
							<div class="card alert">
								<div class="card-header">
									<h4>Список ботов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Логин</th>
												<th>Трейд ссылка</th>
											</tr>
										</thead>
										<tbody>
                                            <?php foreach($bots as $bot): ?>
                                                <tr>
                                                    <td><?php echo e($bot->username); ?></td>
                                                    <td><a href="<?php echo e($bot->trade); ?>"><?php echo e($bot->trade); ?></a></td>
                                                </tr>
                                            <?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->

            <script>
                // $('button').click(function() {
                //     var botid = parseFloat($(this).attr('data-botid'));
                //     if(isNaN(botid)) return toastr.error('Не удалось найти ID бота!');
                //     var state = NaN;
                //     if($(this).hasClass('setOnline')) {
                //         $(this).removeClass('btn-success');
                //         $(this).addClass('btn-danger');
                //         $(this).removeClass('setOnline');
                //         $(this).addClass('setOffline');
                //         $(this).html('<i class="ti-settings"></i>Выключить');
                //         state = 1;
                //     } else if($(this).hasClass('setOffline')) {
                //         $(this).removeClass('btn-danger');
                //         $(this).addClass('btn-success');
                //         $(this).removeClass('setOffline');
                //         $(this).addClass('setOnline');
                //         $(this).html('<i class="ti-settings"></i>Включить');
                //         state = 0;
                //     }
                //
                //     if(isNaN(state)) return toastr.error('Не удалось понять действие, которое Вы хотите совершить!');
                //
                //     $.ajax({
                //         url : '/admin/setBotOnline',
                //         type : 'post',
                //         data : {
                //             id : botid,
                //             online : state
                //         },
                //         success : function(data) {
                //             if(data.success) {
                //                 toastr.success(data.msg);
                //             } else {
                //                 toastr.error(data.msg);
                //             }
                //         },
                //         error : function(err) {
                //             toastr.error('Ошибка! Попробуйте чуть позже!');
                //             console.log(err.responseText);
                //         }
                //     });
                // });
                $('.bot_online').click(function() {
                    var type = $(this).attr('data-type');
                    var bot = $(this).attr('data-bot-type');

                    $.ajax({
                        url : '/admin/setBotOnline',
                        type : 'post',
                        data : {
                            type : type,
                            bot : bot
                        },
                        success : function(data) {
                            console.log(data);
                        },
                        error : function(data) {
                            toastr.error('Ошибка при отправке данных на сервер!');
                            console.log(data.responseText);
                        }
                    });
                });
            </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>