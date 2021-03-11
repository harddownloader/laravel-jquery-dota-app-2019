@extends('admin')

@section('content')
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Предметы</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Предметы</li>
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
									<h4>Список выводов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table-4" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>WITHDRAW ID</th>
                                                <th>BOT ID</th>
												<th>Пользователь</th>
												<th>Предметы</th>
                                                <th>Цена</th>
                                                <th>Статус</th>
                                                <th>Дата</th>
											</tr>
										</thead>
										<tbody>
                                            @foreach($list['withdraws'] as $w)
                                                <tr>
                                                    <td>{{ $w->with_id }}</td>
                                                    <td>{{ $w->bot_id }}</td>
                                                    <td><a href="/admin/user_edit/{{ $w->user->id }}"><img src="{{ $w->user->avatar }}" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> {{ $w->user->username }}</a></td>
                                                    <td>
                                                        @foreach($w->items as $item)
                                                            <img src="https://cdn.dota2.net/item/{{ $item->market_hash_name }}/30.png" alt="" style="float : left; margin : 2px; width : 60px;" title="{{ $item->market_hash_name }} - {{ floor(($item->price/100)*(100+$config->with_percent)) }}">
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $w->price }}</td>
                                                    <td>
                                                        @if($w->status == 0)
                                                            В очереди
                                                        @elseif($w->status == 1)
                                                            Обрабатывается
                                                        @elseif($w->status == 2)
                                                            Подтверждается
                                                        @elseif($w->status == 3)
                                                            Отправлен
                                                        @elseif($w->status == 4)
                                                            Принят
                                                        @elseif($w->status == 5)
                                                            Отклонен
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($w->updated_at)->addHours(1)->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                            @endforeach
										</tbody>
									</table>
								</div>
							</div><!-- /# card -->
						</div><!-- /# column -->
					</div><!-- /# row -->
                    <div class="row">
						<div class="col-lg-12">
							<div class="card alert">
								<div class="card-header">
									<h4>Список депозитов </h4>
								</div>
								<div class="bootstrap-data-table-panel">
									<table id="bootstrap-data-table-5" class="table table-striped table-bordered">
										<thead>
											<tr>
                                                <th>ID</th>
												<th>Пользователь</th>
												<th>Предметы</th>
                                                <th>Цена</th>
                                                <th>Статус</th>
                                                <th>Дата</th>
                                            </thead>
											</tr>
										<tbody>
                                             @foreach($list['deposits'] as $w)
                                                <tr>
                                                    <td>
                                                        @if(!is_null($w->offer_id))
                                                            {{ $w->offer_id }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td><a href="/admin/user_edit/{{ $w->user->id }}"><img src="{{ $w->user->avatar }}" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> {{ $w->user->username }}</a></td>
                                                    <td>
                                                        @foreach($w->items as $item)
                                                            <img src="https://cdn.dota2.net/item/{{ $item->market_hash_name }}/30.png" alt="" style="float : left; margin : 2px; width : 60px;" title="{{ $item->market_hash_name }} - {{ $item->price }}">
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $w->price }}</td>
                                                    <td>
                                                        @if($w->status == 0)
                                                            В очереди
                                                        @elseif($w->status == 1)
                                                            Обрабатывается
                                                        @elseif($w->status == 2)
                                                            Отправлен
                                                        @elseif($w->status == 3)
                                                            Принят
                                                        @elseif($w->status == 4)
                                                            Отклонен
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($w->updated_at)->addHours(1)->format('Y-m-d H:i:s') }}</td>
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

            <script>
                $(document).ready(function() {
                    $('#bootstrap-data-table-4').DataTable({
                        order : [[0, 'desc']]
                    });
                    $('#bootstrap-data-table-5').DataTable({
                        order : [[0, 'desc']]
                    });
                });
            </script>
@endsection
