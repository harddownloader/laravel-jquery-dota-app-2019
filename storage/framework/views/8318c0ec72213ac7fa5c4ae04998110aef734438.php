

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование изображения #<?php echo e($image->id); ?></h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Настройка чата</li>
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

                                        <form action="/admin/bandit/bandit_save_image" method="post">
                                            <input name="id" value="<?php echo e($image->id); ?>" hidden>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>URL</label>
                                                        <input type="text" class="form-control" name="url" value="<?php echo e($image->url); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (1 линия)</label>
                                                        <input type="text" class="form-control" name="multiplier" value="<?php echo e($image->multiplier); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (2 линии)</label>
                                                        <input type="text" class="form-control" name="multiplier2" value="<?php echo e($image->multiplier2); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (3 линии)</label>
                                                        <input type="text" class="form-control" name="multiplier3" value="<?php echo e($image->multiplier3); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (4 линии)</label>
                                                        <input type="text" class="form-control" name="multiplier4" value="<?php echo e($image->multiplier4); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (5 линий)</label>
                                                        <input type="text" class="form-control" name="multiplier5" value="<?php echo e($image->multiplier5); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (6 линий)</label>
                                                        <input type="text" class="form-control" name="multiplier6" value="<?php echo e($image->multiplier6); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (7 линий)</label>
                                                        <input type="text" class="form-control" name="multiplier7" value="<?php echo e($image->multiplier7); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (8 линий)</label>
                                                        <input type="text" class="form-control" name="multiplier8" value="<?php echo e($image->multiplier8); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Множитель (ЧЕРЕЗ ЗАПЯТУЮ) х3 х4 х5 (одинаковых изображений на линии выплат) (9 линий)</label>
                                                        <input type="text" class="form-control" name="multiplier9" value="<?php echo e($image->multiplier9); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Тип изображения</label>
                                                        <select class="form-control" name="type">
                                                            <?php if($image->type == 0): ?>
                                                                <option value="0">Обычный</option>
                                                                <option value="1">Wild</option>
                                                                <option value="2">Free Spins</option>
                                                            <?php elseif($image->type == 1): ?>
                                                                <option value="1">Wild</option>
                                                                <option value="0">Обычный</option>
                                                                <option value="2">Free Spins</option>
                                                            <?php elseif($image->type == 2): ?>
                                                                <option value="2">Free Spins</option>
                                                                <option value="0">Обычный</option>
                                                                <option value="1">Wild</option>
                                                            <?php else: ?>
                                                                <option value="0">Обычный</option>
                                                                <option value="1">Wild</option>
                                                                <option value="2">Free Spins</option>
                                                            <?php endif; ?>
                                                        </select>
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