

<?php $__env->startSection('content'); ?>
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Предметы</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Предметы</li>
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
									<h4>Список выводов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table-4" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>WITHDRAW ID</th>
                                                <th>BOT ID</th>
												<th>Пользователь</th>
												<th>Предметы</th>
                                                <th>Цена</th>
                                                <th>Статус</th>
                                                <th>Дата</th>
											</tr>
										</thead>
										<tbody>
                                            <?php foreach($list['withdraws'] as $w): ?>
                                                <tr>
                                                    <td><?php echo e($w->with_id); ?></td>
                                                    <td><?php echo e($w->bot_id); ?></td>
                                                    <td><a href="/admin/user_edit/<?php echo e($w->user->id); ?>"><img src="<?php echo e($w->user->avatar); ?>" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> <?php echo e($w->user->username); ?></a></td>
                                                    <td>
                                                        <?php foreach($w->items as $item): ?>
                                                            <img src="https://cdn.dota2.net/item/<?php echo e($item->market_hash_name); ?>/30.png" alt="" style="float : left; margin : 2px; width : 60px;" title="<?php echo e($item->market_hash_name); ?> - <?php echo e(floor(($item->price/100)*(100+$config->with_percent))); ?>">
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td><?php echo e($w->price); ?></td>
                                                    <td>
                                                        <?php if($w->status == 0): ?>
                                                            В очереди
                                                        <?php elseif($w->status == 1): ?>
                                                            Обрабатывается
                                                        <?php elseif($w->status == 2): ?>
                                                            Подтверждается
                                                        <?php elseif($w->status == 3): ?>
                                                            Отправлен
                                                        <?php elseif($w->status == 4): ?>
                                                            Принят
                                                        <?php elseif($w->status == 5): ?>
                                                            Отклонен
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($w->updated_at)->addHours(1)->format('Y-m-d H:i:s')); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
                    <div class="row">
						<div class="col-lg-12">
							<div class="card alert">
								<div class="card-header">
									<h4>Список депозитов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table-5" class="table table-striped table-bordered">
										<thead>
											<tr>
                                                <th>ID</th>
												<th>Пользователь</th>
												<th>Предметы</th>
                                                <th>Цена</th>
                                                <th>Статус</th>
                                                <th>Дата</th>
                                            </thead>
											</tr>
										<tbody>
                                             <?php foreach($list['deposits'] as $w): ?>
                                                <tr>
                                                    <td>
                                                        <?php if(!is_null($w->offer_id)): ?>
                                                            <?php echo e($w->offer_id); ?>

                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><a href="/admin/user_edit/<?php echo e($w->user->id); ?>"><img src="<?php echo e($w->user->avatar); ?>" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> <?php echo e($w->user->username); ?></a></td>
                                                    <td>
                                                        <?php foreach($w->items as $item): ?>
                                                            <img src="https://cdn.dota2.net/item/<?php echo e($item->market_hash_name); ?>/30.png" alt="" style="float : left; margin : 2px; width : 60px;" title="<?php echo e($item->market_hash_name); ?> - <?php echo e($item->price); ?>">
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td><?php echo e($w->price); ?></td>
                                                    <td>
                                                        <?php if($w->status == 0): ?>
                                                            В очереди
                                                        <?php elseif($w->status == 1): ?>
                                                            Обрабатывается
                                                        <?php elseif($w->status == 2): ?>
                                                            Отправлен
                                                        <?php elseif($w->status == 3): ?>
                                                            Принят
                                                        <?php elseif($w->status == 4): ?>
                                                            Отклонен
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($w->updated_at)->addHours(1)->format('Y-m-d H:i:s')); ?></td>
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
                $(document).ready(function() {
                    $('#bootstrap-data-table-4').DataTable({
                        order : [[0, 'desc']]
                    });
                    $('#bootstrap-data-table-5').DataTable({
                        order : [[0, 'desc']]
                    });
                });
            </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>