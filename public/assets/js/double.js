$(document).ready(function() {

    // Sounds :
    var doubleWin = new Audio('assets/frontend/sounds/roulette-win.mp3'),
        doubleLose = new Audio('assets/frontend/sounds/roulette-lose.mp3'),
        doubleBet = new Audio('assets/frontend/sounds/roulette-bet.mp3'),
        doubleStart = new Audio('assets/frontend/sounds/roulette-start.mp3');

    function playCount(i) {
        if($('#sound-btn').hasClass('mute')) return;
        switch (i) {
            case 3:
                i = 1;
            break;
            case 2:
                i = 2;
            break;
            case 1:
                i = 3;
            break;
            default:
                return;
            break;
        }
        var audio = new Audio('assets/frontend/sounds/count'+i+'.mp3');
        audio.play();
    }

    var socket = io.connect('https://dotaregal.com:8443', {secure:true});

    socket.emit('getRotate');
    socket.on('getRotate', function(e) {
        if((e.success) && (e.time > 0)) {
            $('#circle').css('transition', 'transform ' + Math.floor(e.time/10) + 's');
            $('#circle').css('transform', 'rotate(' + e.rotate + 'deg)');
            if(!$('#sound-btn').hasClass('mute')) doubleStart.play();
            setTimeout(function() {
                var list = $('.bets-list');
                for(var i = 0; i < list.length; i++) if($(list[i]).attr('data-type') != e.type) $(list[i]).css('opacity', '0.5');
                // doubleStart.stop();
            }, e.time*100);
        }

    });

    $('.calc-panel span').click(function() {
        var value = parseFloat($('#calc-number').val());
        if(isNaN(value)) value = 0;
        switch($(this).attr('data-method')) {
            case 'plus' :
                value += parseFloat($(this).attr('data-value'));
            break;
            case 'max' :
                $.ajax({
                    url : '/getMyBalance',
                    type : 'post',
                    success : function(data) {
                        $('#calc-number').val(data.balance);
                    },
                    error : function(err) {
                        toastr.error('Ошибка!');
                    }
                });
            break;
        }
        $('#calc-number').val(value);
    });

    $('.calc-panel .circle').click(function() {
        var value = parseFloat($('#calc-number').val());
        if(isNaN(value) || value < 1) return toastr.error('Вы забыли указать сумму ставки!');
        $('#calc-number').val(0);
        $.ajax({
            url : '/double/addBet',
            type : 'post',
            data : {
                value : value,
                type : $(this).attr('data-type')
            },
            success : function(data) {
                if(data.success) {
                    toastr.success(data.msg);
                    $('.balance').text(data.balance);
                    if(!$('#sound-btn').hasClass('mute')) doubleBet.play();
                } else {
                    if(typeof data.msg != 'undefined') {
                        toastr.error(data.msg);
                    } else {
                        toastr.error('Вам необходимо авторизироваться!');
                    }
                }
            },
            error : function(data) {
                console.log(data.responseText);
            }
        });
    });

    socket.on('double.new.bet', function(data) {
        var value2 = data.bet.value;

        if((typeof value2 == 'string') && (value2.indexOf('K') != -1)) {
            value2 = value2.replace('K', '');
            value2 = parseFloat(value2)*1000;
        }

        var list = $('.top_bet');
        for(var i = 0; i < list.length; i++) {
            if(data.top[$(list[i]).attr('data-type')]) {
                $(list[i]).find('.top_user').text(data.top[$(list[i]).attr('data-type')].count + ' USERS');
                if(data.top[$(list[i]).attr('data-type')].value != null) $(list[i]).find('.top_value').html(data.top[$(list[i]).attr('data-type')].value + '<div class="coin noselect"></div>');
            }
        }

        var color = '';
        if(typeof data.bet.user_id != 'undefined' && data.bet.user_id == USER_ID) color = 'yellow';

        var html = '';
            html += '<tr data-value="' + value2 + '">'
            html += '<td style="white-space : nowpar; overflow : hidden; color : '+color+';">' + data.bet.username + '</td>';
            html += '<td>' + data.bet.value + '<div class="coin noselect"></div></td>';
            html += '</tr>';

        var list = $('.bets');

        for(var i = 0; i < list.length; i++) if($(list[i]).attr('data-type') == data.bet.type) {
            var bets = $(list[i]).find('tr');
            if(bets.length != 0) {
                for(var u = 0; u < bets.length; u++) {
                    if($(bets[u]).find('td:first').text() == data.bet.username) {
                        var value = $(bets[u]).find('td:last').text();
                        if(value.indexOf('K') != -1) {
                            value = value.replace('K', '');
                            value = parseFloat(value)*1000;
                        } else {
                            value = parseFloat(value);
                        }

                        value += value2;

                        $(bets[u]).attr('data-value', value);
                        if(value >= 1000) value = (value/1000).toFixed(0) + 'K';
                        $(bets[u]).find('td:last').html((value) + '<div class="coin noselect"></div>');
                        return;
                    }
                }
            }
        }

        for(var i = 0; i < list.length; i++) if($(list[i]).attr('data-type') == data.bet.type) {
            var bets = $(list[i]).find('tr');
            if(bets.length == 0) {
                $(list[i]).html(html);
                return;
            }
            var found = false;
            for(var u = 0; u < bets.length; u++) {
                if(parseFloat($(bets[u]).attr('data-value')) < value2 && !found) {
                    $(bets[u]).before(html);
                    found = true;
                }
            }
            if(!found) {
                $(list[i]).append(html);
            }
        }
    });

    socket.on('double.timer', function(data) {
        var time = data.time
        // if(data.type == 'ts' && !$('#sound-btn').hasClass('mute')) playCount(time);
        if(time < 10) time = '0' + time;
        $('.timer').text(time);
        // doubleTimer.play();
    });

    socket.on('double.new.game', function(data) {

        // $('.title span').text(0);
        $('.bets').slideDown('linear', function() {
            $('.bets tr').remove();
            $('.bets').show();
        });
        $('.top_bet').find('.top_user').text('0 USERS');
        $('.top_bet').find('.top_value').html('0<div class="coin noselect"></div>');
        $('.bets').css('opacity', 1);

        $('#circle').css('transition', '0s');
        $('#circle').css('transform', 'rotate(' + data.rotate + 'deg)');

        $('.bets-list').css('opacity', 1);
    });

    socket.on('double.result', function(data) {
        if(data.user_id == USER_ID && !$('#sound-btn').hasClass('mute')) if(data.result) doubleWin.play(); else doubleLose.play();
    });

    $('#calc-number').focus(function() {
        $(this).val('');
    });
});
