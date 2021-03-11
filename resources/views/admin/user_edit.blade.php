@extends('admin')

@section('content')
          <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Редактирование пользователя {{ $user->username }}</h1>
                            </div>
                        </div>
                    </div><!-- /# column -->
                    <div class="col-lg-4 p-0">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="/admin">Панель управления</a></li>
                                    <li class="active">Редактирование пользователя</li>
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
										
                                        <form action="/admin/save_user" method="post">
                                            <input type="text" name="id" value="{{ $user->id }}" hidden>
                                            <div class="row">
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Ник</label>
                                                        <input type="text" class="form-control" value="{{ $user->username }}" name="username">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Аватар</label>
                                                        <input type="text" class="form-control" value="{{ $user->avatar }}" name="avatar">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Права</label>
                                                        <select name="permission" value="{{ $user->permission }}" class="form-control">
                                                            @if($user->permission == 2)
                                                                <option value="2">Администратор</option>
                                                                <option value="0">Пользователь</option>
                                                                <option value="1">Модератор</option>
                                                            @elseif($user->permission == 1)
                                                                <option value="1">Модератор</option>
                                                                <option value="0">Пользователь</option>
                                                                <option value="2">Администратор</option>
                                                            @else
                                                                <option value="0">Пользователь</option>
                                                                <option value="1">Модератор</option>
                                                                <option value="2">Администратор</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Трейд-ссылка</label>
                                                        <input type="text" class="form-control" value="{{ $user->trade }}" name="trade">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Баланс</label>
                                                        <input type="number" class="form-control" value="{{ $user->money }}" name="money">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Префикс государства</label>
                                                        <input type="text" class="form-control" value="{{ $user->flagState }}" name="flagState">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Бан</label>
                                                        <select name="is_banned" class="form-control">
                                                            @if($user->is_banned)
                                                                <option value="1">Да</option>
                                                                <option value="0">Нет</option>
                                                            @else
                                                                <option value="0">Нет</option>
                                                                <option value="1">Да</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Уровень</label>
                                                        <input type="numer" class="form-control" value="{{ $user->lvl }}" name="lvl">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Необходимый опыт</label>
                                                        <input type="numer" class="form-control" value="{{ $user->n_xp }}" name="n_xp">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Опыт</label>
                                                        <input type="numer" class="form-control" value="{{ $user->xp }}" name="xp">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в бандите</label>
                                                        <input type="numer" class="form-control" value="{{ $user->slot_machine }}" name="slot_machine">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в рулетке</label>
                                                        <input type="numer" class="form-control" value="{{ $user->roulette }}" name="roulette">
                                                    </div>
                                                </div>
												
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Кол-во игр в покере</label>
                                                        <input type="numer" class="form-control" value="{{ $user->poker }}" name="poker">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Реферальный код</label>
                                                        <input type="numer" class="form-control" value="{{ $user->ref }}" name="ref">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Код владельца</label>
                                                        <input type="numer" class="form-control" value="{{ $user->my_ref }}" name="my_ref">
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