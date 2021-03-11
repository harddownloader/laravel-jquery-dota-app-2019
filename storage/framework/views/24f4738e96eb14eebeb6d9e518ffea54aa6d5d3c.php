

<?php $__env->startSection('content'); ?>
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Пользователи</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Пользователи</li>
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
									<h4>Список пользователей </h4>
								</div>
                                <div class="col-md-4 pull-right" style="margin-right: -48px;">  
                                    <div class="col-md-3" style="line-height: 42px;text-align: right;font-size: 16px;margin-right: -18px;">  
                                        <label>Search:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control search">
                                    </div>
                                </div>
                                <script>
                                    function ban()
                                    {
                                        console.log($(this));
                                        return;
                                        $('button').click(function() {
                                            var userid = parseFloat($($el).attr('data-userid'));
                                            var is_banned = NaN;
                                            if(isNaN(userid)) toastr.error('Не удалось найти ID пользователя!');
                                            if($($el).hasClass('ban_user')) {
                                                $($el).removeClass('ban_user');
                                                $($el).removeClass('btn-danger');
                                                $($el).addClass('unban_user');
                                                $($el).addClass('btn-success');
                                                $($el).html('<i class="ti-settings"></i>Разбанить');
                                                is_banned = 1;
                                            } else if($($el).hasClass('unban_user')) {
                                                $($el).removeClass('unban_user');
                                                $($el).removeClass('btn-success');
                                                $($el).addClass('ban_user');
                                                $($el).addClass('btn-danger');
                                                $($el).html('<i class="ti-settings"></i>Забанить');
                                                is_banned = 0;
                                            }
                                            
                                            if(isNaN(is_banned)) toastr.error('Не удалось понять Ваш выбор.');
                                            
                                            $.ajax({
                                                url     : '/admin/banUser',
                                                type    : 'post',
                                                data    : {
                                                    arg : is_banned,
                                                    id  : userid
                                                },
                                                success : function(data) {
                                                    if(data.success) {
                                                        toastr.success(data.msg);
                                                    } else {
                                                        if(typeof data.msg != 'undefined') {
                                                            toastr.error(data.msg);
                                                        } else {
                                                            toastr.error('Ошибка!');
                                                        }
                                                    }
                                                },
                                                error   : function(err) {
                                                    toastr.error('Ошибка при отправке на сервер!');
                                                    console.log(err.responseText);
                                                }
                                            });
                                        });
                                    }
                                </script>
								<div class="bootstrap-data-table-panel">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th data-col="username" data-sort="desc" class="table-event" style="cursor: pointer;">Ник</th>
												<th data-col="money" data-sort="desc" class="table-event" style="cursor: pointer;">Баланс</th>
                                                <th data-col="permission" data-sort="desc" class="table-event" style="cursor: pointer;">Права</th>
												<th data-col="ref" data-sort="desc" class="table-event" style="cursor: pointer;">Код</th>
												<th>Редактировать</th>
											</tr>
										</thead>
										<tbody id="app">
                                            
                                            <tr v-for="user in users">
                                                <td><img v-bind:src="user.avatar" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> {{ user.username }}</td>
                                                <td>{{ user.money }}</td>
                                                <td>
                                                    <span v-if="user.permission == 0">Пользователь</span>
                                                    <span v-else>Администратор</span>
                                                </td>
                                                <td>{{ user.ref }}</td>
                                                <td>
                                                    <button v-if="user.is_banned == 1" type="button" class="btn btn-success btn-flat btn-addon btn-sm m-l-5 unban_user" v-bind:data-userid="user.id" v-on:click="ban()"><i class="ti-settings"></i>Разбанить</button>
                                                    <button v-else type="button" class="btn btn-danger btn-flat btn-addon btn-sm m-l-5 ban_user" v-bind:data-userid="user.id" v-on:click="ban()"><i class="ti-settings"></i>Забанить</button>
                                                    <a v-bind:href="'/admin/user_edit/' + user.id" class="btn btn-info btn-flat btn-addon btn-sm m-l-5 edit_user"><i class="ti-settings"></i>Редактировать</a>
                                                </td>
                                            </tr>
                                            
										</tbody>
									</table>
								</div>
                                <script>
                                    var app = new Vue({
                                        el : '#app',
                                        data : {
                                            users : []
                                        },
                                        updated : function() {
                                            $('.table-event').removeClass('stopped');

                                        }                                    
                                    });

                                    $.ajax({
                                        url : '/admin/getUsers',
                                        type : 'post',
                                        data : {
                                            col : 'id',
                                            sort : 'desc'
                                        },
                                        success : function(data) {
                                            app.users = data;
                                        }
                                    });

                                    $('.table-event').click(function() {
                                        if($(this).hasClass('stopped')) return;

                                        $.ajax({
                                            url : '/admin/getUsers',
                                            type : 'post',
                                            data : {
                                                col : $(this).attr('data-col'),
                                                sort : $(this).attr('data-sort')
                                            },
                                            success : function(data) {
                                                app.users = data;
                                            }
                                        });

                                        $('.table-event').addClass('stopped');

                                        if($(this).attr('data-sort') == 'desc')
                                        {
                                            $(this).attr('data-sort', 'asc');
                                        } else {
                                            $(this).attr('data-sort', 'desc');
                                        }
                                    });

                                    $('.search').on('keypress', function(e)
                                    {
                                        if(e.which == 13)
                                        {
                                            $.ajax({
                                                url : '/test',
                                                type : 'post',
                                                data : {
                                                    result : $('.search').val()
                                                },
                                                success : function(e)
                                                {
                                                        app.users = e;
                                                },
                                                error : function(e)
                                                {
                                                    toastr.error();
                                                    console.log(e.responseText);
                                                }
                                            });
                                        }
                                    });
                                </script>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>