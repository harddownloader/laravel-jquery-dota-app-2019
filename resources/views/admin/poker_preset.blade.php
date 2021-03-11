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
                                                        <label>Время до начала игры (Bet)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[0]->value }}" name="{{ $preset->args[0]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Время для повышения ставки / пропуска (Raise/Check)</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[1]->value }}" name="{{ $preset->args[1]->name }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Режим внесения ставки [0 - обычный, 1 - новый]</label>
                                                        <input type="number" class="form-control" value="{{ $preset->args[2]->value }}" name="{{ $preset->args[2]->name }}">
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
