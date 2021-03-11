<?php
/* PAGES */

#login
Route::get('/login', ['as' => 'login', 'uses' => 'AuthController@login']);

#home page
Route::get('/', ['as' => 'home', 'uses' => 'PagesController@index']);

Route::get('/lang/{lang}', 'PagesController@lang');
Route::get('/tutorial', ['uses' => 'PagesController@tutorial'])->name('tutorial');

# Auth middleware
Route::group(['middleware' => 'auth'], function() {

    #roulette
    Route::get('/double', ['as' => 'roulette', 'uses' => 'DoubleController@index']);

    #logout
    Route::get('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

    #profile
    Route::get('/profile', ['as' => 'profile', 'uses' => 'PagesController@profile']);
 
    #poker
    Route::get('/poker', ['as' => 'poker', 'uses' => 'PokerController@index']);
    
    #deposit
    Route::get('/deposit', ['as' => 'deposit', 'uses' => 'BotsController@deposit']);

    #shop
    Route::get('/withdraw', ['as' => 'withdraw', 'uses' => 'BotsController@shop']);


    #slot machine
    Route::get('/bandit', ['as' => 'slot.machine', 'uses' => 'BanditController@index', 'middleware' => 'web']);
    Route::get('/bandit/paytable', ['as' => 'paytable', 'uses' => 'BanditController@PayTable']);
    Route::get('/bandit/paytable/{lines}/{bet}', ['as' => 'paytable2', 'uses' => 'BanditController@paytable2']);
});

# Support
Route::post('/support', 'PagesController@support');


Route::group(['middleware' => 'ban'], function() {
    # Deposit post
    Route::post('/deposit_parse', 'BotsController@deposit_parse');
    Route::post('/deposit_send', 'BotsController@deposit_send');

    # Withdraw post
    Route::post('/shop_parse', 'BotsController@shop_parse');
    Route::post('/shop_send', 'BotsController@shop_send');

    # Users post messages
    Route::post('/double/addBet', 'DoubleController@addBet');
    Route::post('/chat/send', 'ChatController@sendMessage');

    # Promo post
    Route::post('/promo/redeem', 'PromoController@redeemPromo');

    # Save Trade Url
    Route::post('/saveUrl', 'AuthController@updateSettings');
    Route::post('/getMyBalance', 'PagesController@getMyBalance');

    # Bandit

    Route::post('/bandit/finish_game', 'BanditController@finishGame');
    Route::post('/bandit/maxbet', 'BanditController@getMaxBet');
    Route::post('/bandit/newGame', 'BanditController@newGame');
    Route::post('/bandit/checkFree', 'BanditController@checkFree');
    Route::post('/bandit/mgame', 'BanditController@mgame');
    Route::post('/bandit/quest', 'BanditController@quest');
    Route::post('/bandit/towers', 'BanditController@towers');
    Route::post('/bandit/getLines', 'BanditController@getOtherLines');

    Route::post('/poker/start', 'PokerController@start');
    Route::post('/poker/checkResult', 'PokerController@checkResult');
    Route::post('/poker/autofold', 'PokerController@AutoFold');
});

Route::group(['middleware' => 'access'], function() {

    #pocker

    Route::get('/test/{user_id}', 'BanditController@test');

    Route::post('/test', 'AdminController@test');  






    Route::get('/insert', 'AchievementController@insert');
    Route::get('/profitTest', 'BanditController@profitTest');
    Route::get('/repair', 'BotsController@repairShop');
    Route::get('/upd', 'BotsController@updateTable');
    Route::get('/upd2', 'BotsController@updatePrice');
});

# Admin get
Route::get('/admin', ['uses' => 'AdminController@index', 'middleware' => 'access']);
Route::group(['prefix' => 'admin', 'middleware' => 'access'], function () {
    // Route::get('/test/{steamid64}', 'PagesController@test2');
    Route::get('/antiminus', 'AdminController@antiminus');
    Route::post('/getUsers', 'AdminController@usersGet');
    Route::get('/settings', 'AdminController@settings');
    Route::get('/shop_settings', 'AdminController@shop_settings');
    Route::get('/users', 'AdminController@users');
    Route::get('/user_edit/{id}', 'AdminController@user_edit');
    Route::get('/bots', 'AdminController@bots');
    Route::get('/bot_edit/{id}', 'AdminController@bot_edit');
    Route::get('/poker', 'AdminController@poker');
    Route::get('/double', 'AdminController@double');
    Route::get('/bandit', 'AdminController@bandit');
    Route::get('/chat', 'AdminController@chat');
    Route::get('/bandit/delete_combo/{id}', 'AdminController@bandit_delete_combo');
    Route::get('/bandit/edit_combo/{id}', 'AdminController@bandit_edit_combo');
    Route::get('/bandit/edit_image/{id}', 'AdminController@bandit_edit_image');
    Route::get('/proxy', 'AdminController@proxy');
    Route::get('/proxy/{id}', 'AdminController@edit_proxy');
    Route::get('/promo', 'PromoController@index');
    Route::get('/promo/{id}', 'PromoController@edit');
    Route::get('/promo/delete/{id}', 'PromoController@deletePromo');
    Route::get('/items', 'AdminController@itemsList');

    Route::post('/preset/set', 'AdminController@set_preset');
    Route::get('/preset/edit/{id}', 'AdminController@edit_preset');
    Route::get('/preset/del/{id}', 'AdminController@del_preset');
    Route::post('/preset/add', 'AdminController@add_preset');
    Route::post('/preset/save', 'AdminController@save_preset');
});

# Admin post1
Route::group(['prefix' => 'admin', 'middleware' => 'access'], function() {
    Route::post('/antiminus/save', 'AdminController@antiminusSave');
    Route::post('/promo/create', 'PromoController@createNewPromo');
    Route::post('/promo/save', 'PromoController@savePromo');
    Route::post('/save_user', 'AdminController@save_user');
    Route::post('/banUser', 'AdminController@banUser');
    Route::post('/setBotOnline', 'AdminController@setBotOnline');
    Route::post('/save_bot', 'AdminController@save_bot');
    Route::post('/settings_save', 'AdminController@settings_save');
    Route::post('/save_shop_settings', 'AdminController@save_shop_settings');
    Route::post('/save_double', 'AdminController@save_double');
    Route::post('/save_bandit', 'AdminController@save_bandit');
    Route::post('/save_poker', 'AdminController@save_poker');
    Route::post('/save_chat', 'AdminController@save_chat');
    Route::post('/bandit/add_img', 'AdminController@bandit_add_img');
    Route::post('/bandit/add_combo', 'AdminController@bandit_add_combo');
    Route::post('/bandit/save_bandit_combo', 'AdminController@save_bandit_combo');
    Route::post('/bandit/bandit_save_image', 'AdminController@bandit_save_image');
    Route::post('/proxy/save', 'AdminController@save_proxy');
    Route::post('/getDiagrams', 'AdminController@getDiagrams');
    Route::patch('/settings/is_dark_theme/{type}', 'AdminController@updateDarkTheme');
});

#Route::get('/withdraw/getOffers', 'BotsController@getOffers');
#Route::get('/withdraw/getList/{with_id}', 'BotsController@getList');

# Api
Route::group(['prefix' => 'api', 'middleware' => 'secretKey'], function() {
    Route::post('/double/getSlider', 'DoubleController@getSlider');
    Route::post('/double/getStatus', 'DoubleController@getStatus');
    Route::post('/double/newGame', 'DoubleController@newGame');
    Route::post('/chat/double', 'ChatController@DoubleFakeMessages');
    Route::post('/double/fakeBets', 'DoubleController@fakeBets');
    Route::post('/bots/getOwner', 'BotsController@getOwner');
    Route::post('/bots/getBots', 'BotsController@getBots');
    Route::post('/withdraw/getOffers', 'BotsController@getOffers');
    Route::post('/withdraw/getList', 'BotsController@getList');
    Route::post('/withdraw/addOffer', 'BotsController@withdraw_addoffer');
    Route::post('/withdraw/update_status', 'BotsController@withdraw_update_status');
    Route::post('/addDepOffer', 'BotsController@deposit_addoffer');
    Route::post('/updateDepOffer', 'BotsController@deposit_updoffer');
    Route::post('/addIp', 'AuthController@addIp');
    Route::post('/sendOtherItems', 'BotsController@sendOtherItems');
    Route::post('/updateInventory', 'BotsController@updateInventory');
    Route::post('/declineWithdrawById', 'BotsController@declineWithdrawById');
    Route::post('/updatePrice', 'BotsController@updatePrice');


    Route::post('/checkDeposits', 'BotsController@checkDeposits');
    Route::post('/checkWithdraws', 'BotsController@checkWithdraws');
    Route::post('/updateWithdrawStatus', 'BotsController@updateWithdrawStatus');
    Route::post('/updateDepositStatus', 'BotsController@updateDepositStatus');
    Route::post('/declineWithdraw', 'BotsController@declineWithdraw');
    Route::post('/declineDeposit', 'BotsController@declineDeposit');
    Route::post('/getOffers', 'BotsController@getOffers');
    Route::post('/sendNotify', 'BotsController@sendNotify');
    
    
    
    Route::post('/doubleQueue', 'DoubleController@checkQueue');
    Route::post('/promoQueue', 'PromoController@checkQueue');
});

    Route::get('/poker/profit/{ante}/{trips}', 'PokerController@profitTest');