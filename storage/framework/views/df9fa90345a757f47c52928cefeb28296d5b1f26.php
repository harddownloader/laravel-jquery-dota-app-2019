

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование промокода #<?php echo e($promo->id); ?></h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Промокоды</li>
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

                                        <form action="/admin/promo/save" method="post">
                                            <input name="id" value="<?php echo e($promo->id); ?>" hidden>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Промокод</label>
                                                        <input type="text" class="form-control" name="promo" value="<?php echo e($promo->promo); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Вознаграждение за активацию</label>
                                                        <input type="text" class="form-control" name="price" value="<?php echo e($promo->money); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во возможных активаций</label>
                                                        <input type="text" class="form-control" name="count" value="<?php echo e($promo->count); ?>">
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