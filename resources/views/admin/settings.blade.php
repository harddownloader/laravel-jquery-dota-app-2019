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
					<div class="row">
						<div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-body">
                                    <div class="basic-elements">

                                        <form action="/admin/settings_save" method="post">
                                            <div class="row">

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Заголовок</label>
                                                        <input type="text" class="form-control" value="{{ $config->sitename }}" name="sitename">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Описание сайта (Для поисковых систем)</label>
                                                        <input type="text" class="form-control" value="{{ $config->descriptions }}" name="descriptions">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Ключевые слова (Для поисковых систем, указывать через запятую.)</label>
                                                        <input type="text" class="form-control" value="{{ $config->keywords }}" name="keywords">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Email для отправления support запросов.</label>
                                                        <input type="text" class="form-control" value="{{ $config->site_email }}" name="site_email">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12" style="height: 30px;"></div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Facebook</label>
                                                        <input type="text" class="form-control" value="{{ $config->facebook }}" name="facebook">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>ВКонтакте</label>
                                                        <input type="text" class="form-control" value="{{ $config->vk }}" name="vk">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>YouTube</label>
                                                        <input type="text" class="form-control" value="{{ $config->youtube }}" name="youtube">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Twitter</label>
                                                        <input type="text" class="form-control" value="{{ $config->twitter }}" name="twitter">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12" style="height: 30px;"></div>


                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Включение оповещения.</label>
                                                        <select name="alert_active" class="form-control">
                                                            @if($config->alert_active)
                                                                <option value="1">Включено</option>
                                                                <option value="0">Выключено</option>
                                                            @else
                                                                <option value="0">Выключено</option>
                                                                <option value="1">Включено</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Сообщение при выводе оповещения. [ru]</label>
                                                        <input type="text" class="form-control" value="{{ $config->alert_message_ru }}" name="alert_message_ru">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label style="margin-top: 19px;">Сообщение при выводе оповещения. [en]</label>
                                                        <input type="text" class="form-control" value="{{ $config->alert_message_en }}" name="alert_message_en">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Цвет окна оповещения. [error - красный, warning - желтый, info - синий, success - зеленый]</label>
                                                        <input type="text" class="form-control" value="{{ $config->alert_type }}" name="alert_type">
                                                    </div>
                                                </div>

                                                <div class="col-lg-12" style="height: 30px;"></div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Сумма, которую даем пользователю, чей код активировали</label>
                                                        <input type="number" class="form-control" value="{{ $config->ref_own_money }}" name="ref_own_money">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Сумма, которую даем пользователю, при активировации кода</label>
                                                        <input type="number" class="form-control" value="{{ $config->ref_rem_money }}" name="ref_rem_money">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>% дохода от рефералов [0-100]</label>
                                                        <input type="number" class="form-control" value="{{ $config->ref_percent }}" name="ref_percent">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Максимальное кол-во рефералов</label>
                                                        <input type="number" class="form-control" value="{{ $config->ref_count }}" name="ref_count">
                                                    </div>
                                                </div>

                                                <!-- <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Создавать ли виртуальную активность на сайте?</label>
                                                        <select name="ii_active" class="form-control">
                                                            @if($config->ii_active)
                                                                <option value="1">Да</option>
                                                                <option value="0">Нет</option>
                                                            @else
                                                                <option value="0">Нет</option>
                                                                <option value="1">Да</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div> -->

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

            <script src="{{ asset('assets/js/ajaxForm.js') }}"></script>
@endsection
