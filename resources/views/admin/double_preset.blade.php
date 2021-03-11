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
                                                        <label>Время таймера (сек.)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[0]->value }}" name="{{ $preset->args[0]->name }}">
                                                    </div>
                                                </div>


                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время прокрутки барабана (сек.)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[1]->value }}" name="{{ $preset->args[1]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время до новой игры (сек.)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[2]->value }}" name="{{ $preset->args[2]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальное количество игроков для старта</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[3]->value }}" name="{{ $preset->args[3]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Минимальная сумма ставки</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[4]->value }}" name="{{ $preset->args[4]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальная сумма ставки (0 = Выкл)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[5]->value }}" name="{{ $preset->args[5]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>На какое кол-во множителей может поставить игрок за одну игру (4 - ОПАСНО!)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[6]->value }}" name="{{ $preset->args[6]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12"></div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "blue"</label>
                                                        <input type="text" class="form-control" value="{{ $preset->args[7]->value }}" name="{{ $preset->args[7]->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "green"</label>
                                                        <input type="text" class="form-control" value="{{ $preset->args[8]->value }}" name="{{ $preset->args[8]->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "yellow"</label>
                                                        <input type="text" class="form-control" value="{{ $preset->args[9]->value }}" name="{{ $preset->args[9]->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Шанс выпадения "red"</label>
                                                        <input type="text" class="form-control" value="{{ $preset->args[10]->value }}" name="{{ $preset->args[10]->name }}">
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
