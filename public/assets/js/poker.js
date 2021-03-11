$(document).ready(function() {

    console.log('hello');

    var doubleWin = new Audio('assets/frontend/sounds/roulette-win.mp3');
        doubleLose = new Audio('assets/frontend/sounds/poker_lose.mp3');

    $('.character2').hide();
    $('.character').show();

    var proceed = [];

    var game = false,
        bet_type = false,
        game_status = GAME_STATUS;

    if(GAME_STATUS < 1) $('#myModalBet').modal('show');

    if(GAME_STATUS > 0) game = true;

    if(ANTE > 0) updateValue([$('.poker-ante')], ANTE);
    if(TRIPS > 0) updateValue([$('.poker-trips')], TRIPS);
    if(BLIND > 0) updateValue([$('.poker-blind')], BLIND);
    if(BET > 0) updateValue([$('.poker-play')], BET);

    $('.poker-play').click(function() {
        if(game) return;
        startGame();
    });

    var trips = 0,
        ante = 0,
        blind = 0;

    var value = NaN,
        typeOfValue = null,
        timer = false,
        check_timer,
        bet_timer,
        cards = [],
        was_played = false,
        dealerCardsCount = 0,
        cardList = [],
        dealer_cards = 0;

    if(BET > 0) was_played = true;

    console.log(GAME_STATUS);

    showResModal(game_status);

    $('.poker_change_value').click(function() {
        value = parseFloat($(this).attr('data-value'));
        typeOfValue = 'number';
        $('.poker_change_value').css('border', '');
        $('.poker-multiplier').css('border', '');
        if(isNaN(value)) return toastr.error('Вы забыли выбрать номинал ставки!');

        $(this).css('border', '1px solid #fff');

        if(PutVariable == 'trips') {
            if(ante < 1) return toastr.error('Вы должны сначала сделать ставку на анте!');
            trips += value;
            proceed.push({
                index : 'trips',
                type : 'number',
                value : value
            });
            updateBet();
            updateValue([$('.poker-trips')], trips);
        } else if(PutVariable == 'ante') {
            ante += value;
            blind += value;
            proceed.push({
                index : 'ante',
                type : 'number',
                value : value
            });
            updateBet();
            updateValue([$('.poker-ante')], ante);
            updateValue([$('.poker-blind')], blind);
            if(ante > 0) checkTimer('bet');
        }
    });

    $('.poker-trips').click(function() {
        if(game) return;
        if(ante < 1) return toastr.error('Вы должны сначала сделать ставку на анте!');

        if($(this).hasClass('not_active')) {
            $('.poker-ante').removeClass('blue');
            $(this).addClass('blue');
            $(this).removeClass('not_active');
            $('.poker-ante').addClass('not_active');
            PutVariable = 'trips';
            return;
        }

        if(typeOfValue == 'number') trips += value; else if(typeOfValue == 'multiplier' && trips > 0) trips *= value;
        updateValue([$('.poker-trips')], trips);
        proceed.push({
            index : 'trips',
            type : typeOfValue,
            value : value
        });

        updateBet();
    });

    $('.poker-ante').click(function() {
        if(game) return;

        if(isNaN(value)) return toastr.error('Вы забыли выбрать номинал ставки!');


        if($(this).hasClass('not_active')) {
            $('.poker-trips').removeClass('blue');
            $(this).addClass('blue');
            $(this).removeClass('not_active');
            $('.poker-trips').addClass('not_active');
            PutVariable = 'ante';
            return;
        }

        if(typeOfValue == 'number') {
            blind += value;
            ante += value;
            updateValue([$('.poker-ante'), $('.poker-blind')], ante);
        } else if(typeOfValue == 'multiplier' && ante > 0) {
            blind *= value;
            ante *= value;
            updateValue([$('.poker-ante'), $('.poker-blind')], ante);
        }
        proceed.push({
            index : 'ante',
            type : typeOfValue,
            value : value
        });
        updateBet();
        if(ante > 0) checkTimer('bet');
    });

    $('.btn-clear').click(function()
    {
        ante = 0;
        blind = 0;
        trips = 0;
        $('.poker-trips').html('TRIPS');
        $('.poker-ante').html('ANTE');
        $('.poker-blind').html('BLIND');
        updateBet();

        clearInterval(bet_timer);

        $('#myModalBet .sep').stop();
        $('#myModalBet').find('#bet-timer').text(BET_TIMER);
        $('#myModalBet .sep').css('width', '140px');

        timer = false;
    });

    $('.btn-undo').click(function()
    {
        if($(this).hasClass('btn-paytable')) return;
        var obj = proceed[proceed.length-1];
        delete proceed[proceed.length-1];
        var list = [];
        for(var i = 0; i < proceed.length; i++) if(typeof proceed[i] != 'undefined') list.push(proceed[i]);
        proceed = list;

        console.log(proceed);

        if(obj.type == 'number') {
            if(obj.index == 'ante') {
                ante -= obj.value;
                blind -= obj.value;
            } else if(obj.index == 'trips') {
                trips -= obj.value;
            }
        } else if(obj.type == 'multiplier') {
            if(obj.index == 'ante') {
                ante /= obj.value;
                blind /= obj.value;
            } else if(obj.index == 'trips') {
                trips /= obj.value;
            }
        }
        updateValue([$('.poker-trips')], trips);
        updateValue([$('.poker-ante'), $('.poker-blind')], ante);
        if(trips <= 0) $('.poker-trips').html('TRIPS');
        if(ante <= 0) {
            $('.poker-ante').html('ANTE');
            clearInterval(bet_timer);

            $('#myModalBet .sep').stop();
            $('#myModalBet').find('#bet-timer').text(BET_TIMER);
            $('#myModalBet .sep').css('width', '140px');
            timer = false;
        }
        if(blind <= 0) $('.poker-blind').html('BLIND');
        updateBet();
    });

    $('.poker-multiplier').click(function() {
        value = parseFloat($(this).attr('data-value'));
        typeOfValue = 'multiplier';
        if(isNaN(value)) return toastr.error('Ошибка! Попробуйте чуть позже.');

        $('.poker_change_value').css('border', '');
        $('.poker-multiplier').css('border', '');
        $(this).css('border', '1px solid #fff');

        if(PutVariable == 'trips') {
            if(ante < 1) return toastr.error('Вы должны сначала сделать ставку на анте!');
            trips *= value;
            proceed.push({
                index : 'trips',
                type : 'multiplier',
                value : value
            });
            updateBet();
            updateValue([$('.poker-trips')], trips);
        } else if(PutVariable == 'ante') {
            ante *= value;
            blind *= value;
            proceed.push({
                index : 'ante',
                type : 'multiplier',
                value : value
            });
            updateBet();
            updateValue([$('.poker-ante')], ante);
            updateValue([$('.poker-blind')], blind);
            if(ante > 0) checkTimer('bet');
        }
    });

    function updateBet()
    {
        var sum = trips+ante+blind;
        if(sum != parseFloat(sum.toFixed(0))) sum = sum.toFixed(1); else sum = sum.toFixed(0);
        $('#bet').text(sum);
    }

    function checkTimer(type)
    {
        if(timer) return;
        var time;
        switch (type) {
            case 'check':
                timer = true;
                time = RAISE_TIMER;
                $('#myModalChoose .sep').animate({width : '0px'}, ((time*1000)+1000), function() {
                    $('#myModalChoose').modal('hide');
                });
                check_timer = setInterval(function() {
                    time--;
                    $('#check-timer').text(time);
                    if(time <= 0) {
                        stopResModal();
                        timer = false;
                        game = false;
                        AutoFold();
                    }
                }, 1000);
                break;
            case 'bet' :
                timer = true;
                time = BET_TIMER;
                $('#myModalBet .sep').animate({width : '0px'}, ((time*1000)+1000), function() {
                    $('#myModalBet').modal('hide');
                });
                bet_timer = setInterval(function() {
                    time--;
                    $('#bet-timer').text(time);
                    if(time <= 0) {
                        clearInterval(bet_timer);
                        timer = false;
                        if(!game) startGame();
                    }
                }, 1000);
                break;

        }

    }

    function startGame()
    {
        $.ajax({
            url : '/poker/start',
            type : 'post',
            data : {
                ante : ante,
                blind : blind,
                trips : trips
            },
            success : function(data) {
                if(data.success) {
                    proceed = [];
                    ConstructGame(data.status, data.data);
                    game = true;
                } else {
                    toastr.error(data.msg);
                    ConstructGame(5, false);
                }
            },
            error : function(res) {
                toastr.error('Ошибка при отправлении данных на сервер!');
                console.log(res.responseText);
            }
        });
    }

    function AutoFold()
    {
        $.ajax({
            url : '/poker/autofold',
            type : 'post',
            success : function(res) {
                $('#myModalChoose').modal('hide');
                ConstructGame(5);
            },
            error : function(res) {
                toastr.error('Ошибка при отправке данных на сервер!');
                console.log(res.responseText);
            }
        });
    }

    function ConstructGame(status, data)
    {
        game_status = status;
        switch (status) {
            case 1 :
                game = true;

                if(!$('.poker-trips').hasClass('not_active')) $('.poker-trips').addClass('not_active');
                $('.poker-trips, .poker-ante').removeClass('blue');

                dealer_cards = 0;
                cardList = [];
                $('#myModalBet').modal('hide');
                $('#myModalBids #myModalLabel').text('BETS ACCEPTED');
                $('#myModalBids').modal('show');
                setTimeout(function() {
                    $('#myModalBids').modal('hide');
                    setTimeout(function() {
                        $('.character').hide();
                        $('.character2').show();
                    }, 1500)
                    $('#mycoins').text(data.balance);
                    $('#totalcoins').text(data.total);
                    var type = 'user';
                    var key = 1;
                    var cardListener = setInterval(function() {
                        if(type == 'user') {
                            showTableCards(data.cards, key, 'bottom');
                            // $('#my-cards').append('<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.cards[key].id + '_' + data.cards[key].section + '.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>');
                            if($('#player-cards-block').css('display') == 'none') $('#player-cards-block').slideDown();
                            showCards(data.shows, (key-1));
                            type = 'diler';
                            key++;
                        } else if(type == 'diler') {
                            dealer_cards++;
                            var html = '';
                            if(key == 2) {
                                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>';
                                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%; opacity : 0;"></div>';
                            } else if(key == 3) {
                                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>';
                                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>';
                            }

                            $('#field-enemy-cards').html(html);

                            type = 'user';
                            if(key == 3) {
                                $('.character2').hide();
                                $('.character').show();
                                clearInterval(cardListener);
                                setTimeout(function() {
                                    var html = '';
                                        html += '<div class="bet chip-3x check_result" data-res="x3"></div>';
                                        html += '<div class="bet chip-4x check_result" data-res="x4"></div>';
                                        html += '<div class="bet chip-check check_result" data-res="check"></div>';
                                    $('#checkResult').html(html);

                                    checkResultEvent(game_status);

                                    if(!was_played) $('#myModalChoose').modal('show');
                                    if(!was_played) checkTimer('check');
                                }, 1000);
                            }
                        }
                    }, 2500);
                }, 1000);
                break;
            case 2 :
            $('.character').hide();
            $('.character2').show();
                game = true;
                $('#mycoins').text(data.balance);
                $('#totalcoins').text(data.total);
                if(data.bet != 0) updateValue([$('.poker-play')], data.bet);
                var key = 2;
                var cardListener = setInterval(function() {
                    dealer_cards++;
                    showTableCards(data.cards, key-1, 'middle');
                    // $('#game-cards').append('<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.cards[key].id + '_' + data.cards[key].section + '.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>');
                    showCards(data.shows, key);
                    // $('#dealer-cards-block').find('#player-cards').append('<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat;"></div>');
                    key++;
                    if(key == 5) {
                        $('.character2').hide();
                        $('.character').show();
                        clearInterval(cardListener);
                        setTimeout(function() {
                            var html = '';
                                html += '<div class="bet chip-2x check_result" data-res="x2"></div>';
                                html += '<div class="bet chip-check check_result" data-res="check"></div>';
                            $('#checkResult').html(html);

                            checkResultEvent(game_status);

                            if(!was_played) $('#myModalChoose').modal('show');
                            if(!was_played) checkTimer('check');
                        }, 1000);
                    }
                }, 2500);
                break;
            case 3 :
            $('.character').hide();
            $('.character2').show();
                game = true;
                $('#mycoins').text(data.balance);
                $('#totalcoins').text(data.total);
                if(data.bet != 0) updateValue([$('.poker-play')], data.bet);
                var key = 5;
                var cardListener = setInterval(function() {
                    showTableCards(data.cards, key-1, 'middle');

                    dealer_cards++;
                    // $('#game-cards').append('<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.cards[key].id + '_' + data.cards[key].section + '.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>');
                    showCards(data.shows, key);
                    // $('#dealer-cards-block').find('#player-cards').append('<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat;"></div>');
                    key++;
                    if(key == 7) {
                        $('.character2').hide();
                        $('.character').show();
                        clearInterval(cardListener);
                        setTimeout(function() {
                            var html = '';
                                html += '<div class="bet chip-1x check_result" data-res="x1"></div>';
                                html += '<div class="bet chip-fold check_result" data-res="fold"></div>';
                            $('#checkResult').html(html);

                            checkResultEvent(game_status);

                            if(!was_played) $('#myModalChoose').modal('show');
                            if(!was_played) checkTimer('check');
                        }, 1000);
                    }
                }, 2500);
                break;
            case 4 :
                // var list = $('#field-enemy-cards .card');
                if($('.poker-play .chip-img').length == 0) updateValue([$('.poker-play')], data.bet2);
                $('#totalcoins').text(data.total2);
                $('#mycoins').text(data.balance2);
                game = true;
                var html = '';
                for(var i = 0; i < 2; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.dc[i].id + '_' + data.dc[i].section + '.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>';
                $('#field-enemy-cards').html(html);
                var html = '';
                // for(var i = 0; i < data.dealer.used.length; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.dealer.used[i].id + '_' + data.dealer.used[i].section + '.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat;"></div>';
                for(var i = 0; i < data.dealer.used.length; i++) {
                    if(data.dealer.used[i].used) {
                        html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.dealer.used[i].id + '_' + data.dealer.used[i].section + '.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>';
                    } else {
                        html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + data.dealer.used[i].id + '_' + data.dealer.used[i].section + '.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5;"></div>';
                    }
                }
                $('#dealer-cards-block').find('#player-cards').html(html);
                $('#dealer-combo').text(data.dealer.name);
                $('#dealer-cards-block').slideDown();
                $('#dealer-combo').slideDown();
                setTimeout(function() {
                    $('#mycoins').text(data.balance);
                    $('#totalcoins').text(data.total);
                    if(data.win && data.gameStatus == 0) {

                        if(!$('#sound-btn').hasClass('mute')) {
                            doubleWin.play();
                        }



                        $('#user-combo').addClass('yellow-text');
                        $('#user_win').text('YOU WIN');
                        $('#diler_win').removeClass('yellow-text');
                        $('#user_win').addClass('yellow-text');
                        $('#dealer-combo').removeClass('yellow-text');
                        $('#user_win').slideDown();
                        // $('#myModalBids #myModalLabel').text('YOU WIN : 5000 COINS!');
                    } else if(!data.win && data.gameStatus < 0) {

                        if(!$('#sound-btn').hasClass('mute')) {
                            doubleLose.play();
                        }


                        $('#dealer-combo').addClass('yellow-text');
                        $('#diler_win').addClass('yellow-text');
                        $('#user-combo').removeClass('yellow-text');
                        $('#diler_win').text('DEALER WIN');
                        // $('#myModalBids #myModalLabel').text('YOU LOSE!');
                        $('#diler_win').slideDown();
                    }
                    updateValue([$('.poker-ante')], data.ante);
                    if($('.poker-trips .chip-img').length > 0) updateValue([$('.poker-trips')], data.trips);
                    updateValue([$('.poker-blind')], data.blind);
                    updateValue([$('.poker-play')], data.bet);
                }, 2000);
                setTimeout(function() {
                    if(data.gameStatus == 2) {
                        $('#myModalBids').find('#myModalLabel').text(IDENTITY_MSG + data.total);
                    } else if(data.gameStatus == 1) {
                        $('#myModalBids').find('#myModalLabel').text(NGAME_MSG + data.total);
                    } else if(data.gameStatus == 0) {
                        $('#myModalBids').find('#myModalLabel').text(WIN_MSG + data.total);
                    } else {
                        $('#myModalBids').find('#myModalLabel').text(LOSE_MSG);
                    }
                    $('#myModalBids').modal('show');
                    setTimeout(function() {
                        $('#myModalBids').modal('hide');
                        setTimeout(function() {
                            ConstructGame(5, undefined);
                        }, 2000);
                    }, 1500);
                }, 6000);
                break;
            case 5 :
                clearInterval(bet_timer);
                stopResModal();

                $('#myModalBet .sep').stop();
                game = false;
                cards = [];
                $('#totalcoins').text(0);
                $('#user_win').slideUp();
                $('#player-cards-block').slideUp(function() {
                    $('#player-cards-block').find('#player-cards').html('');
                    // $('#player-cards .card').slideDown();
                });
                $('#user-combo').text('NONE');

                $('#dealer-cards-block').slideUp(function() {
                    $('#dealer-cards-block #player-cards .card').remove();
                    $('#diler_win').slideUp();
                    $('#dealer-combo').text('NONE');
                    $('#dealer-combo').slideUp();
                });

                $('#my-cards .card').remove();
                $('#field-enemy-cards .card').remove();
                $('#game-cards .card').remove();

                $('.poker-trips').html('TRIPS');
                $('.poker-ante').html('ANTE');
                $('.poker-ante').addClass('blue');
                $('.poker-blind').html('BLIND');
                $('.poker-play').html('PLAY');

                $('#user-combo').removeClass('yellow-text');
                $('#dealer-combo').removeClass('yellow-text');

                ante = 0;
                blind = 0;
                trips = 0;
                was_played = false;


                timer = false;
                $('#bet').text(0);
                $('#myModalBet').find('#bet-timer').text(BET_TIMER);
                $('#myModalBet .sep').css('width', '140px');
                $('#myModalBet').modal('show');

                $('.poker-ante').addClass('blue');
                $('.poker-ante').removeClass('not_active');
                PutVariable = 'ante';
                break;
        }
        console.log('Constructed (' + status + ')');
        console.log(data);
        console.log('_______________________________');
    }

    function checkResultEvent(status)
    {
        timer = false;
        if(!was_played) {
            $('.check_result').click(function(e) {
                    stopResModal();
                    $.ajax({
                        url : '/poker/checkResult',
                        type : 'post',
                        data : {
                            result : $(this).attr('data-res')
                        },
                        success : function(data) {
                            if(data.success) {
                                ConstructGame(data.status, data.data);

                                switch ($(e.currentTarget).attr('data-res')) {
                                    case 'x1':
                                         $('#user-status').text('YOU PLAYED');
                                         was_played = true;
                                        break;
                                    case 'x2':
                                         $('#user-status').text('YOU PLAYED');
                                         was_played = true;
                                        break;
                                    case 'x3':
                                         $('#user-status').text('YOU PLAYED');
                                         was_played = true;
                                        break;
                                    case 'x4':
                                         $('#user-status').text('YOU PLAYED');
                                         was_played = true;
                                        break;
                                    case 'check':
                                         $('#user-status').text('YOU CHECKED');
                                        break;
                                    case 'fold':
                                        $('#user-status').text('YOU FOLDED');
                                        break;
                                }

                                $('#user-status').slideDown();

                                setTimeout(function() {
                                    $('#user-status').slideUp();
                                }, 3000);
                            } else {
                                console.log('STATUS - ' + status);
                                setTimeout(function() {
                                    showResModal(status);
                                }, 500);
                                toastr.error(data.msg);
                            }
                        },
                        error : function(res) {
                            toastr.error('Ошибка при отправке данных на сервер!');
                            console.log(res.responseText);
                        }
                    });
            });
        } else {
            setTimeout(function() {
                $.ajax({
                    url : '/poker/checkResult',
                    type : 'post',
                    success : function(data) {
                        stopResModal();
                        ConstructGame(data.status, data.data);
                    },
                    error : function(res) {
                        toastr.error('Ошибка при отправке данных на сервер!');
                        console.log(res.responseText);
                    }
                });

                $('#user-status').slideDown();

                setTimeout(function() {
                    $('#user-status').slideUp();
                }, 3000);
            }, 3000);
        }
    }

    function showResModal(status)
    {
        status = parseFloat(status);
        console.log(status);
        if(status < 1 || status > 3) return;
        var html = '';
        game = true;
        switch(status)
        {
            case 1 :
                html += '<div class="bet chip-3x check_result" data-res="x3"></div>';
                html += '<div class="bet chip-4x check_result" data-res="x4"></div>';
                html += '<div class="bet chip-check check_result" data-res="check"></div>';
            break;
            case 2 :
                html += '<div class="bet chip-2x check_result" data-res="x2"></div>';
                html += '<div class="bet chip-check check_result" data-res="check"></div>';
                break;
            case 3 :
                html += '<div class="bet chip-1x check_result" data-res="x1"></div>';
                html += '<div class="bet chip-fold check_result" data-res="fold"></div>';
                break;
        }

        $('#checkResult').html(html);

        checkResultEvent(game_status);

        if(!was_played) $('#myModalChoose').modal('show');
        if(!was_played) checkTimer('check');
    }

    function stopResModal()
    {
        $('#myModalChoose .sep').stop();
        clearInterval(check_timer);
        $('#myModalChoose .sep').css('width', '140px');
        $('#myModalChoose').modal('hide');
        $('#check-timer').text(RAISE_TIMER);
    }

    function updateValue($el, $value)
    {
        var $style, $chip;
        if($value < 1) $chip = 'bottom-chip-05'
        if($value >= 1) $chip = 'bottom-chip-1';
        if($value >= 5) $chip = 'bottom-chip-5';
        if($value >= 25) $chip = 'bottom-chip-25';
        if($value >= 100) $chip = 'bottom-chip-100';
        if($value >= 500) $chip = 'bottom-chip-500';

        if($value >= 1000) {
            $value = $value/1000;
            if($value != Math.floor($value)) {
                $value = $value.toFixed(1);
                $style = '  font-size : 7px; line-height : 490%; padding-left : 1px; color : #fff;';
            } else {
                $style = '  line-height : 295%; color : #fff; padding-left : 1px;'
            }

            if($value >= 10) $style = '  color : #fff; line-height : 695%; padding-left : 1px; font-size : 5px;';
            $value += 'K';
        }

        for(var i = 0; i < $el.length; i++) $($el[i]).html('<div class="chip-img ' + $chip + ' style="' + $style + '"">' + $value + '</div>&nbsp;');
    }

    function rotateElement($el,$img)
    {
        // background-size: contain;background-position: 50% 100%; background-repeat : no-repeat;
        $el.css({
            'transition': 'transform 0.5s',
            'transform':"rotateY(90deg)",
            'transform' : 'rotateX(30deg)',
            'background-size' : 'contain',
            'background-position' : '50% 100%',
            'background-repeat' : 'no-repeat'
        }).on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function()
        {

            $el.css({
                'transition': 'transform 0s',
                'background' : "url("+$img+")",
                'transform' : 'rotateX(30deg)',
                'background-size' : 'contain',
                'background-position' : '50% 100%',
                'background-repeat' : 'no-repeat'
            });

            $el.css({
                'transition': 'transform 0.5s',
                'transform':"rotateY(0deg)",
                'transform' : 'rotateX(30deg)',
                'background-size' : 'contain',
                'background-position' : '50% 100%',
                'background-repeat' : 'no-repeat'
            });
            //
            // $el.css('background-size', 'contain');
            // $el.css('background-position', '50% 100%');
            // $el.css('background-repeat', 'no-repeat');
            // $el.css('transform', 'rotateX(30deg)');

        });
    }

    function showCards(list, key)
    {
        var count = 5;
        if(list[key].cards.length < count) count = list[key].cards.length;
        var html = '';
        for(var i = 0; i < count; i++) {
            if(list[key].cards[i].used) {
                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + list[key].cards[i].id + '_' + list[key].cards[i].section + '.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 1;"></div>';
            } else {
                html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + list[key].cards[i].id + '_' + list[key].cards[i].section + '.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity: 0.5"></div>';
            }
        }

        for(var i = count; i < 5; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity : 0;"></div>';
        $('#player-cards-block').find('#player-cards').html(html);

        $('#user-combo').text(list[key].result.name);
        if($('#user-combo').css('display') == 'none') $('#user-combo').slideDown();

        console.log('Cards was showed!');
    }

    function showTableCards(list, count, place)
    {
        console.log(list);
        console.log(count);
        var html = '';
            for(var i = 0; i < count; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/' + list[i].id + '_' + list[i].section + '.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%;"></div>';
            for(var i = count; i < list.length; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-repeat: no-repeat;height: 50px; background-position: 50% 100%; opacity : 0;"></div>';

        var pos;

        switch (place) {
            case 'top':
                    pos = '#field-enemy-cards';
                break;
            case 'middle' :
                    pos = '#game-cards';
                break;
            case 'bottom' :
                    pos = '#my-cards';
                break;
        }

        $(pos).html(html);

        console.log('Table cards was showed!');
    }

    // function showDealer()
    // {
    //     return;
    //     var html = '';
    //     for(var i = 0; i < dealer_cards; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat;"></div>';
    //     for(var i = dealer_cards; i < 7; i++) html += '<div class="card" style="background : url(http://dotaregal.com/assets/images/cards/short.png); background-size: contain;background-position: 50% 100%; background-repeat : no-repeat; opacity : 0;"></div>';
    //     $('#dealer-cards-block').find('#player-cards').html(html);
    // }

});
