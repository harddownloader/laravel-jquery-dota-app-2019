

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Настройка игры "Дабл"</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Игры</li>
                                    <li class="active">Дабл</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /# column -->
                </div><!-- /# row -->
                <div class="main-content">
                    <?php if(isset($presets[0])): ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-header">
                                    <h4>Пресеты</h4>
                                </div>
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form class="not-auto">  
                                            <div class="row">

 

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Выберите один из пресетов</label>
                                                        <select id="preset_id" class="form-control">
                                                            <?php foreach($presets as $preset): ?>
                                                                <option value="<?php echo e($preset->id); ?>"><?php echo e($preset->name); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <button  data-id="<?php echo e($presets[0]->id); ?>" class="btn btn-success btn-flat btn-addon btn-sl m-l-5 set_preset"><i class="ti-check"></i>Активировать пресет</button>                                                  
                                                    <a href="/admin/preset/edit/<?php echo e($presets[0]->id); ?>" class="btn btn-info btn-flat btn-addon btn-sl m-l-5 edit_preset"><i class="ti-check"></i>Редактировать пресет</a>
                                                    <a href="/admin/preset/del/<?php echo e($presets[0]->id); ?>" class="btn btn-danger btn-flat btn-addon btn-sl m-l-5 del_preset"><i class="ti-check"></i>Удалить пресет</a>
                                                </div>

                                            </div>
                                        </form>
                                        
                                        <script>
                                            $('#preset_id').change(function() {
                                                $('.edit_preset').attr('href', '/admin/preset/edit/' + $('#preset_id').val());
                                                $('.del_preset').attr('href', '/admin/preset/del/' + $('#preset_id').val());
                                                $('.set_preset').attr('data-id',  $('#preset_id').val());
                                            });
                                            $('.set_preset').click(function(e) {
                                                $.ajax({
                                                    url : '/admin/preset/set',
                                                    type : 'post',
                                                    data : {
                                                        id : $(this).attr('data-id')
                                                    },
                                                    success : function(res) {
                                                        if(res.success) {
                                                            toastr.success(res.msg);
                                                        } else {
                                                            if(typeof res.msg != 'undefined') {
                                                                toastr.error(res.msg);
                                                            } else {
                                                                toastr.error('Ошибка при отправке данных на сервер!');
                                                            }
                                                        }
                                                    },
                                                    error : function(err) {
                                                        toastr.error('Ошибка при отправке данных на сервер!');
                                                        console.log(err.responseText);
                                                    }
                                                });
                                                e.preventDefault();
                                            });
                                        </script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /# row -->
                    <?php endif; ?>
					<div class="row">
						<div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form action="/admin/save_double" method="post" id="SaveDouble">
                                            <div class="row">

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время таймера (сек.)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_timer); ?>" name="double_timer">
                                                    </div>
                                                </div>


                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время прокрутки барабана (сек.)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_timetoslider); ?>" name="double_timetoslider">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время до новой игры (сек.)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_timetonewgame); ?>" name="double_timetonewgame">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальное количество игроков для старта</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_minplayers); ?>" name="double_minplayers">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма ставки</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_min_bet); ?>" name="double_min_bet">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальная сумма ставки (0 = Выкл)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_max_bet); ?>" name="double_max_bet">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>На какое кол-во множителей может поставить игрок за одну игру (4 - ОПАСНО!)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->double_candoit); ?>" name="double_candoit">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12"></div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "blue"</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->double_blue_percent); ?>" name="double_blue_percent">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "green"</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->double_green_percent); ?>" name="double_green_percent">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "yellow"</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->double_yellow_percent); ?>" name="double_yellow_percent">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "red"</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->double_red_percent); ?>" name="double_red_percent">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
													<button type="submit" class="btn btn-success btn-flat btn-addon btn-lg m-l-5"><i class="ti-check"></i>Сохранить</button>
                                                </div>

                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
					</div><!-- /# row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-header">
                                    <h4>Добавить пресет</h4>
                                </div>
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form id="add_preset" class="not-auto">
                                            <div class="row">

 

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Название</label>
                                                        <input type="text" class="form-control" name="name" id="preset_name">
                                                    </div>
                                                </div>


                                                <div class="col-lg-12">
                                                    <button type="submit" class="btn btn-info btn-flat btn-addon btn-sl m-l-5 add_preset"><i class="ti-check"></i>Сохранить пресет</button>
                                                </div>

                                            </div>
                                        </form>
                                        
                                        <script>
                                            $('#add_preset').submit(function(e) {
                                                $.ajax({
                                                    url : '/admin/preset/add',
                                                    type : 'post',
                                                    data : {
                                                        name : $('#preset_name').val(),
                                                        argv : $('#SaveDouble').serializeArray(),
                                                        type : 'double'
                                                    },
                                                    success : function(res) {
                                                        if(res.success) {
                                                            toastr.success(res.msg);
                                                        } else {
                                                            if(typeof res.msg != 'undefined') {
                                                                toastr.error(res.msg);
                                                            } else {
                                                                toastr.error('Ошибка при отправке данных на сервер!');
                                                            }
                                                        }
                                                    },
                                                    error : function(err) {
                                                        toastr.error('Ошибка при отправке данных на сервер!');
                                                        console.log(err.responseText);
                                                    }
                                                });
                                                e.preventDefault();
                                            });
                                        </script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /# row -->
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->

            <script src="<?php echo e(asset('assets/js/ajaxForm.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>