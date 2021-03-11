

<?php $__env->startSection('content'); ?>
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование пресета "<?php echo e($preset->name); ?>"</h1>
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

                                        <form id="SaveForm">
                                            <div class="row">
                                                <input type="hidden" value="<?php echo e($preset->id); ?>" id="id">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Название пресета</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->name); ?>" id="preset_name" name="name">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма ставки</label>
                                                        <input type="number" class="form-control" value="<?php echo e($preset->args[0]->value); ?>" name="<?php echo e($preset->args[0]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальная сумма ставки (0 = выкл)</label>
                                                        <input type="number" class="form-control" value="<?php echo e($preset->args[1]->value); ?>" name="<?php echo e($preset->args[1]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения Free Spins</label>
                                                        <input type="number" class="form-control" value="<?php echo e($preset->args[2]->value); ?>" name="<?php echo e($preset->args[2]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во при выпадении Free Spins (х3, x4, x5)</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[3]->value); ?>" name="<?php echo e($preset->args[3]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 1 линии]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[4]->value); ?>" name="<?php echo e($preset->args[4]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 2 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[5]->value); ?>" name="<?php echo e($preset->args[5]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 3 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[6]->value); ?>" name="<?php echo e($preset->args[6]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 4 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[7]->value); ?>" name="<?php echo e($preset->args[7]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 5 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[8]->value); ?>" name="<?php echo e($preset->args[8]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 6 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[9]->value); ?>" name="<?php echo e($preset->args[9]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 7 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[10]->value); ?>" name="<?php echo e($preset->args[10]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 8 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[11]->value); ?>" name="<?php echo e($preset->args[11]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Процент победы (0-100) [При 9 линиях]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[12]->value); ?>" name="<?php echo e($preset->args[12]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #1 (Множители, спины) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[13]->value); ?>" name="<?php echo e($preset->args[13]->name); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #2 (Иконки) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[14]->value); ?>" name="<?php echo e($preset->args[14]->name); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения игры #3 (Башни) [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[15]->value); ?>" name="<?php echo e($preset->args[15]->name); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения Бонусной Игры [1-100]</label>
                                                        <input type="text" class="form-control" value="<?php echo e($preset->args[16]->value); ?>" name="<?php echo e($preset->args[16]->name); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <button class="btn btn-success btn-flat btn-addon btn-sl m-l-5"><i class="ti-check"></i>Сохранить пресет</button>
                                                </div>
                                            </div>
                                            <script>
                                                $('#SaveForm').submit(function(e) {
                                                    $.ajax({
                                                        url : '/admin/preset/save',
                                                        type : 'post',
                                                        data : {
                                                            id : $('#id').val(),
                                                            argv : $('#SaveForm').serializeArray()
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
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /# row -->
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>