@extends('admin')

@section('content')
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Настройка сайта</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Настройка сайта</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /# column -->
                </div><!-- /# row -->
                <div class="main-content">
                    @if(isset($presets[0]))
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
                                                            @foreach($presets as $preset)
                                                                <option value="{{ $preset->id }}">{{ $preset->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <button  data-id="{{ $presets[0]->id }}" class="btn btn-success btn-flat btn-addon btn-sl m-l-5 set_preset"><i class="ti-check"></i>Активировать пресет</button>                                                  
                                                    <a href="/admin/preset/edit/{{ $presets[0]->id }}" class="btn btn-info btn-flat btn-addon btn-sl m-l-5 edit_preset"><i class="ti-check"></i>Редактировать пресет</a>
                                                    <a href="/admin/preset/del/{{ $presets[0]->id }}" class="btn btn-danger btn-flat btn-addon btn-sl m-l-5 del_preset"><i class="ti-check"></i>Удалить пресет</a>
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
                    @endif
					<div class="row">
						<div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form action="/admin/save_shop_settings" method="post" id="SaveShop">
                                            <div class="row">

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная стоимость итема в трейде (При пополнении баланса)</label>
                                                        <input type="number" class="form-control" value="{{ $config->dep_minprice_item }}" name="dep_minprice_item">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма итемов в трейде (При пополнении баланса)</label>
                                                        <input type="number" class="form-control" value="{{ $config->dep_minprice }}" name="dep_minprice">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная стоимость итема (Для отображения в разделе "Withdraw")</label>
                                                        <input type="number" class="form-control" value="{{ $config->with_minprice_item }}" name="with_minprice_item">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма вывода</label>
                                                        <input type="number" class="form-control" value="{{ $config->with_minprice }}" name="with_minprice">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Курс (1$ = ... игровой валюты)</label>
                                                        <input type="number" class="form-control" value="{{ $config->shop_curs }}" name="shop_curs">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальный уровень для вывода</label>
                                                        <input type="number" class="form-control" value="{{ $config->with_lvl }}" name="with_lvl">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Наценка (%)</label>
                                                        <input type="number" class="form-control" value="{{ $config->with_percent }}" name="with_percent">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимального кол-во вещей для вывода за один раз</label>
                                                        <input type="number" class="form-control" value="{{ $config->with_max_items }}" name="with_max_items">
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
                                                        argv : $('#SaveShop').serializeArray(),
                                                        type : 'shop'
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

            <script src="{{ asset('assets/js/ajaxForm.js') }}"></script>
@endsection
