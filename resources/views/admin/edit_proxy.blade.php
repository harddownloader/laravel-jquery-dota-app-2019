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
                                    <li class="active">Редактирование прокси #{{ $proxy->id }}</li>
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
										
                                        <form action="/admin/proxy/save" method="post">
                                            <div class="row">
												<input type="hidden" name="id" value="{{ $proxy->id }}">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>IP</label>
                                                        <input type="text" class="form-control" value="{{ $proxy->ip }}" name="ip">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Порт</label>
                                                        <input type="text" class="form-control" value="{{ $proxy->port }}" name="port">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Логин</label>
                                                        <input type="text" class="form-control" value="{{ $proxy->login }}" name="login">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Пароль</label>
                                                        <input type="text" class="form-control" value="{{ $proxy->password }}" name="password">
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
            
            <script src="{{ asset('assets/js/ajaxForm.js') }}"></script> 
@endsection