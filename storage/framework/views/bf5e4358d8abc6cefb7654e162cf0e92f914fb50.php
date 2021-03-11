

<?php $__env->startSection('content'); ?>
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Промокоды</h1>
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

                                        <form action="/admin/promo/create" method="post">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Промокод</label>
                                                        <input type="text" class="form-control" name="promo" value="">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Вознаграждение за активацию</label>
                                                        <input type="text" class="form-control" name="price" value="">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Кол-во возможных активаций</label>
                                                        <input type="text" class="form-control" name="count" value="">
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-success btn-flat btn-addon btn-lg m-l-5" style="margin-top : 27px;"><i class="ti-check"></i>Добавить</button>
                                                    </div>
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
									<h4>Список промокодов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table" class="table table-striped table-bordered">
										<thead>
											<tr>
                                                <th>ID</th>
                                                <th>Промокод</th>
                                                <th>Вознаграждение</th>
                                                <th>Кол-во (И/М)</th>
                                                <th>Редактирование</th>
											</tr>
										</thead>
										<tbody>
                                            <?php foreach($list as $promo): ?>
                                                <tr>
                                                    <td><?php echo e($promo->id); ?></td>
                                                    <td><?php echo e($promo->promo); ?></td>
                                                    <td><?php echo e($promo->money); ?></td>
                                                    <td><?php echo e($promo->used); ?>/<?php echo e($promo->count); ?></td>
                                                    <td>
                                                        <a href="/admin/promo/<?php echo e($promo->id); ?>" class="btn btn-info btn-flat btn-addon btn-sm m-l-5 edit_user"><i class="ti-settings"></i>Редактировать</a>
                                                        <a href="/admin/promo/delete/<?php echo e($promo->id); ?>" class="btn btn-danger btn-flat btn-addon btn-sm m-l-5 edit_user"><i class="ti-settings"></i>Удалить</a>
                                                    </td>
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

            <script src="<?php echo e(asset('assets/js/ajaxForm.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>