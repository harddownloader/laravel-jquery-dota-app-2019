@extends('admin')

@section('content')

          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Статистика</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Статистика</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /# column -->
                </div><!-- /# row -->
                <div class="main-content">
					<div class="row">
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
									<div class="stat-icon dib"><i class="ti-money color-success border-success"></i></div>
									<div class="stat-content dib">
										<div class="stat-text">За сегодня</div>
										<div class="stat-digit">${{ $stats['day'] }}</div>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
									<div class="stat-icon dib"><i class="ti-money color-primary border-primary"></i></div>
									<div class="stat-content dib">
										<div class="stat-text">За 7 дней</div>
										<div class="stat-digit">${{ $stats['week'] }}</div>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
									<div class="stat-icon dib"><i class="ti-money color-pink border-pink"></i></div>
									<div class="stat-content dib">
										<div class="stat-text">За месяц</div>
										<div class="stat-digit">${{ $stats['month'] }}</div>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="stat-widget-one">
									<div class="stat-icon dib"><i class="ti-money color-danger border-danger"></i></div>
									<div class="stat-content dib">
										<div class="stat-text">За все время</div>
										<div class="stat-digit">${{ $stats['all'] }}</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-header">
                                    <h4>Статистика за определенный период времени</h4>
                                </div>
                                <button type="button" data-period="day" class="changePeriod btn btn-danger btn-flat btn-sl m-l-5" style="border-radius : 3px;">День</button>
                                <button type="button" data-period="week" class="changePeriod btn btn-warning btn-flat btn-sl m-l-5" style="border-radius : 3px;">Неделя</button>
                                <button type="button" data-period="month" class="changePeriod btn btn-success btn-flat btn-sl m-l-5" style="border-radius : 3px;">Месяц</button>
                                <button type="button" data-period="year" class="changePeriod btn btn-info btn-flat btn-sl m-l-5" style="border-radius : 3px;">Год</button>
                            </div>
                        </div>
                    </div><!-- /# row -->
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="sales-chart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="team-chart"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card alert">
                                <div class="card-header">
                                    <h4>Онлайн : Всего (<span id="online_count">0</span>)</h4>
                                    <br>
                                    Рулетка (<span id="double_count">0</span>)
                                    <br>
                                    Бандит (<span id="bandit_count">0</span>)
                                    <br>
                                    Покер (<span id="poker_count">0</span>)
                                </div>
                                <div class="bootstrap-data-table-panel">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Пользователь</th>
                                                <th>id / steamid64</th>
                                                <th>Открыто страниц</th>
                                            </tr>
                                        </thead>
                                        <tbody id="online">
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- /# card -->
                        </div><!-- /# column -->
                    </div><!-- /# row -->

                    <script type="text/javascript">

                    </script>
				</div><!-- /# main content -->
            </div><!-- /# container-fluid -->

            <script src="{{ asset('assets/js/socket.io-1.3.5.js') }}"></script>
        <script src="{{ asset('assets/js/lib/chart-js/Chart.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/lib/chart-js/chartjs-init.js') }}"></script>
        <script src="{{ asset('assets/js/panel.js') }}"></script>
@endsection
