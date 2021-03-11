

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Игра "Однорукий бандит"</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admins">Панель управления</a></li>
                                    <li class="active">Игры</li>
                                    <li class="active">Однорукий бандит</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /# column -->
                </div><!-- /# row -->
                <div class="main-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
								<div class="card-header">
									<h4>Добавление картинки</h4>
								</div>
								<div class="card-body">
                                    <div class="basic-elements">
                                        <div class="row">
                                            <form action="/admin/bandit/add_img" method="post">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>URL</label>
                                                        <input type="text" class="form-control" name="url">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат)</label>
                                                        <input type="text" class="form-control" name="multiplier">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Тип изображения</label>
                                                        <select class="form-control" name="type">
                                                            <option value="0">Обычный</option>
                                                            <option value="1">Wild</option>
                                                            <option value="2">Free Spins</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <button type="submit" class="btn btn-success btn-flat btn-addon btn-sl m-l-5"><i class="ti-check"></i>Добавить</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
								</div>
                            </div>
                        </div>
                    </div>
					<div class="row">
						<div class="col-lg-12">
							<div class="card alert">
								<div class="card-header">
									<h4>Картинки</h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table" class="table table-striped table-bordered">
										<thead>
											<tr>
                                                <th><center>ID</center></th>
                                                <th><center>Картинка</center></th>
                                                <th><center>Тип</center></th>
                                                <th><center>Множитель</center></th>
                                                <th><center>Редактирование</center></th>
											</tr>
										</thead>
										<tbody>
                                            <?php foreach($images as $image): ?>
                                                <tr>
                                                    <td><center><?php echo e($image->id); ?></center></td>
                                                    <td><center><img src="<?php echo e($image->url); ?>" width="40px" style="border-radius : 50%"></center></td>
                                                    <td><center>
                                                        <?php if($image->type == 0): ?>
                                                            Обычный
                                                        <?php elseif($image->type == 1): ?>
                                                            Wild
                                                        <?php elseif($image->type == 2): ?>
                                                            Free Spins
                                                        <?php else: ?>
                                                            NULL
                                                        <?php endif; ?>
                                                    </center></td>
                                                    <td><center><?php echo e($image->multiplier); ?></center></td>
                                                    <td>
                                                        <center>
                                                            <a href="/admin/bandit/edit_image/<?php echo e($image->id); ?>" class="btn btn-info btn-flat btn-addon btn-sm m-l-5 unban_user"><i class="ti-settings"></i>Редактировать</a>
                                                            <a href="#" class="btn btn-danger btn-flat btn-addon btn-sm m-l-5 unban_user"><i class="ti-settings"></i>Удалить</a>
                                                        </center>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
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
                                <div class="card-header">
                                    <h4>Настройки однорукого бандита</h4>
                                </div>
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form action="/admin/save_bandit" method="post" id="SaveBandit">
                                            <div class="row">

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма ставки</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->bandit_min_bet); ?>" name="bandit_min_bet">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальная сумма ставки (0 = выкл)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->bandit_max_bet); ?>" name="bandit_max_bet">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения Free Spins</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->bandit_free_spins_chance); ?>" name="bandit_free_spins_chance">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во при выпадении Free Spins (х3, x4, x5)</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_free_spins_count); ?>" name="bandit_free_spins_count">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 1 линии]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent); ?>" name="bandit_winpercent">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 2 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent2); ?>" name="bandit_winpercent2">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 3 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent3); ?>" name="bandit_winpercent3">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 4 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent4); ?>" name="bandit_winpercent4">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 5 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent5); ?>" name="bandit_winpercent5">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 6 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent6); ?>" name="bandit_winpercent6">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 7 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent7); ?>" name="bandit_winpercent7">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 8 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent8); ?>" name="bandit_winpercent8">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 9 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_winpercent9); ?>" name="bandit_winpercent9">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #1 (Множители, спины) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_quest); ?>" name="bandit_quest">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #2 (Иконки) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_mgame); ?>" name="bandit_mgame">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #3 (Башни) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_towers); ?>" name="bandit_towers">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения Бонусной Игры [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($config->bandit_bonus); ?>" name="bandit_bonus">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <button type="submit" class="btn btn-success btn-flat btn-addon btn-sl m-l-5"><i class="ti-check"></i>Сохранить</button>
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
                                                        argv : $('#SaveBandit').serializeArray(),
                                                        type : 'bandit'
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