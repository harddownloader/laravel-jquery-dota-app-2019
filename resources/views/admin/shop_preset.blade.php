@extends('admin')

@section('content')
           <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование пресета "{{ $preset->name }}"</h1>
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
                                                <input type="hidden" value="{{ $preset->id }}" id="id">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Название пресета</label>
                                                        <input type="text" class="form-control" value="{{ $preset->name }}" id="preset_name" name="name">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная стоимость итема в трейде (При пополнении баланса)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[0]->value }}" name="{{ $preset->args[0]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма итемов в трейде (При пополнении баланса)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[1]->value }}" name="{{ $preset->args[1]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная стоимость итема (Для отображения в разделе "Withdraw")</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[2]->value }}" name="{{ $preset->args[2]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма вывода</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[3]->value }}" name="{{ $preset->args[3]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Курс (1$ = ... игровой валюты)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[4]->value }}" name="{{ $preset->args[4]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальный уровень для вывода</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[5]->value }}" name="{{ $preset->args[5]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Наценка (%)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[6]->value }}" name="{{ $preset->args[6]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимального кол-во вещей для вывода за один раз</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[7]->value }}" name="{{ $preset->args[7]->name }}">
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

@endsection
