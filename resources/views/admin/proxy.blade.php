@extends('admin')

@section('content')
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
                                    <li class="active">Прокси</li>
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
									<h4>Прокси</h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>ID</th>
												<th>IP:PORT</th>
												<th>Логин</th>
												<th>Пароль</th>
												<th>Редактировать</th>
											</tr>
										</thead>
										<tbody>
                                            @foreach($proxy as $p)
                                                <tr>
                                                    <td>{{ $p->id }}</td>
                                                    <td>{{ $p->ip }}:{{ $p->port }}</td>
                                                    <td>{{ $p->login }}</td>
                                                    <td>{{ $p->password }}</td>
                                                    <td>
                                                        <a href="/admin/delete_proxy/{{ $p->id }}" class="btn btn-danger btn-flat btn-addon btn-sm m-l-5 edit_user"><i class="ti-settings"></i>Удалить</a>
                                                        <a href="/admin/proxy/{{ $p->id }}" class="btn btn-info btn-flat btn-addon btn-sm m-l-5 edit_user"><i class="ti-settings"></i>Редактировать</a>
                                                    </td>
                                                </tr>
                                            @endforeach
										</tbody>
									</table>
								</div>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->
            
@endsection