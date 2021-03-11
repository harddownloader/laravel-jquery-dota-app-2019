

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Настройка чата</h1>
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

                                        <form action="/admin/save_chat" method="post">
                                            <div class="row">

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальное кол-во символов в сообщении (<?php echo e($config->chat_min_strlen); ?>)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->chat_min_strlen); ?>" name="chat_min_strlen">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальное кол-во символов в сообщении (<?php echo e($config->chat_max_strlen); ?>) (0 - выкл)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($config->chat_max_strlen); ?>" name="chat_max_strlen">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Сообщения для ИИ чата рулетки (Разделитель - ; (точка с запятой))</label>
                                                        <div class="form-group">
                                                            <p class="text-muted m-b-15 f-s-12">
                                                                <code>MESSAGE</code> - Текст сообщения.<br>
                                                                <code>WON</code> - 1 : Победа. 0 : Проигрыш. 2: Без разницы.<br>
                                                                <code>MESSAGE : WON;</code> - Пример сообщения (я победил : 1;)
                                                            </p>
                                                        </div>
                                                        <textarea name="chat_double_messages" class="form-control"><?php for($i = 0; $i < count($messages); $i++): ?><?php echo e($messages[$i]->msg); ?> : <?php echo e($messages[$i]->won); ?><?php if($i != count($messages)-1): ?>;<?php endif; ?>&#13;<?php endfor; ?></textarea>
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