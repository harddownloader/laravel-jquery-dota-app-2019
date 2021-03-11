

<?php $__env->startSection('content'); ?>
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование пользователя <?php echo e($user->username); ?></h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Редактирование пользователя</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /# column -->
                </div><!-- /# row -->
                <div class="main-content">
					<div class="row">
						<div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-elements">
										
                                        <form action="/admin/save_user" method="post">
                                            <input type="text" name="id" value="<?php echo e($user->id); ?>" hidden>
                                            <div class="row">
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Ник</label>
                                                        <input type="text" class="form-control" value="<?php echo e($user->username); ?>" name="username">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Аватар</label>
                                                        <input type="text" class="form-control" value="<?php echo e($user->avatar); ?>" name="avatar">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Права</label>
                                                        <select name="permission" value="<?php echo e($user->permission); ?>" class="form-control">
                                                            <?php if($user->permission == 2): ?>
                                                                <option value="2">Администратор</option>
                                                                <option value="0">Пользователь</option>
                                                                <option value="1">Модератор</option>
                                                            <?php elseif($user->permission == 1): ?>
                                                                <option value="1">Модератор</option>
                                                                <option value="0">Пользователь</option>
                                                                <option value="2">Администратор</option>
                                                            <?php else: ?>
                                                                <option value="0">Пользователь</option>
                                                                <option value="1">Модератор</option>
                                                                <option value="2">Администратор</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Трейд-ссылка</label>
                                                        <input type="text" class="form-control" value="<?php echo e($user->trade); ?>" name="trade">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Баланс</label>
                                                        <input type="number" class="form-control" value="<?php echo e($user->money); ?>" name="money">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Префикс государства</label>
                                                        <input type="text" class="form-control" value="<?php echo e($user->flagState); ?>" name="flagState">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Бан</label>
                                                        <select name="is_banned" class="form-control">
                                                            <?php if($user->is_banned): ?>
                                                                <option value="1">Да</option>
                                                                <option value="0">Нет</option>
                                                            <?php else: ?>
                                                                <option value="0">Нет</option>
                                                                <option value="1">Да</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Уровень</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->lvl); ?>" name="lvl">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Необходимый опыт</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->n_xp); ?>" name="n_xp">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Опыт</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->xp); ?>" name="xp">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в бандите</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->slot_machine); ?>" name="slot_machine">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в рулетке</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->roulette); ?>" name="roulette">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в покере</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->poker); ?>" name="poker">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Реферальный код</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->ref); ?>" name="ref">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Код владельца</label>
                                                        <input type="numer" class="form-control" value="<?php echo e($user->my_ref); ?>" name="my_ref">
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
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->
            
            <script src="<?php echo e(asset('assets/js/ajaxForm.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>