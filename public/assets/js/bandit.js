$(document).ready(function() {

    var game = false;
    var linesIDs = [];
    var lineID = 0;
    var bandit_lines = $('.bandit-line');
    var showLines = undefined;
    var freeSpins = false;
    var autoPlay = false;
    var gameRestarter;
    var banditStartSound = new Audio('../assets/frontend/sounds/bandit-start.wav');
    var gameID = 0;
    var towersStatus = false;
    var imagesList = [];

    $.ajax({
        url : '/bandit/getLines',
        type : 'post',
        success : function(data) {
            imagesList = data;
            updateLines();
        }
    });

    function updateLines() {
        var list = $('.slot');
        for(var i = 0; i < list.length; i++) {
            var html = '';
            for(var x = 0; x < 3; x++) {
                var key = Math.floor(Math.random()*imagesList.length);
                html += '<div class="slot-element slot-element-liner" style="background-image: url('+ imagesList[key].url +')"></div>';
            }
            $(list[i]).html(html);
        }
        console.log('LINES WAS UPDATED');

        $('.slot-element').css('opacity', 1);
    }

    // Check Free
    function free() {
        $.ajax({
            url : '/bandit/checkFree',
            type : 'post',
            success : function(res) {
                if(res.success) {
                    freeSpins = true;
                    newGame(0);
                }
                console.log(game + ' - ' + freeSpins);
            }
        });
    }

    setTimeout(function() {
        free();
    }, 1000);

    $('.btn-line').click(function() {
        var value = parseFloat($('#line').text());
        if(isNaN(value)) value = 1;

        switch ($(this).attr('data-type')) {
            case 'minus':
                if(value <= 1) return;
                value--;
                break;
            case 'plus':
                if(value == 9) return;
                value++;
                break;
        }

        var bet = $('#bet').text();

        $('#paytableURL').attr('href', '/bandit/paytable/' + value + '/' + bet);

        if(bet.indexOf('K') != -1) {
            bet = parseFloat(bet.replace('K', ''))*1000;
            totalbet = bet*value;
        } else {
            bet = parseFloat(bet);
            totalbet = bet*value;
        }

        if(totalbet >= 1000) totalbet = (totalbet/1000).toFixed(1) + 'K';

        showLinesByCount(value);

        var lines = $('.slot');

        for(var i = 0; i < lines.length; i++) {
            var multiplier = (i+1)*2;
            $(lines[i]).animate({scrollTop:$(lines[i]).height()*multiplier },0, 'linear');
        }

        $('.totalbet_value').text(totalbet);
        $('.line_value').text(value);
    });

    $('.btn-bet').click(function() {
        var value = $('#bet').text();
        var lines = parseFloat($('#line').text());
        if(isNaN(lines)) {
            lines = 1;
            $('#line').text(lines);
        }
        if(value.indexOf('K') != -1) value = parseFloat(value.replace('K', ''))*1000; else value = parseFloat(value);
        if(isNaN(value)) value = 100;

        switch ($(this).attr('data-type')) {
            case 'minus':
                value -= 100;
                if(value < 100) value = 100;
                break;
            case 'plus':
                value += 100;
                break;
        }

        $('#paytableURL').attr('href', '/bandit/paytable/' + lines + '/' + value);

        var totalbet = 0;

        if(value >= 1000) {
            totalbet = (value*lines).toFixed(1);
            value = (value/1000).toFixed(1);
            $('#bet').text(value + 'K');
        } else {
            $('#bet').text(value.toFixed(0));
            totalbet = (value*lines).toFixed(1);
        }

        if(totalbet >= 1000) totalbet = (totalbet/1000).toFixed(1) + 'K'; else totalbet = parseFloat(totalbet).toFixed(0);

        $('.totalbet_value').text(totalbet);
    });

    $('#btn-maxbet').click(function() {
        $.ajax({
            url : '/bandit/maxbet',
            type : 'post',
            success : function(data) {
                console.log(data);
                if(data.success) {
                    $('#line').text(data.lines);
                    var bet = data.bet;
                    $('#paytableURL').attr('href', '/bandit/paytable/9/' + bet);
                    var isKey = false;
                    if(bet >= 1000) {
                        bet = (bet/1000).toFixed(1);
                        isKey = true;
                    }
                    if(!isKey) $('#bet').text(bet); else $('#bet').text(bet + 'K');
                    if(!isKey) {
                        $('.totalbet_value').text(bet * data.lines);
                    } else {
                        $('.totalbet_value').text((bet * data.lines).toFixed(1) + 'K');
                    }
                } else {
                    toastr.error(data.msg);
                }
            },
            error : function(err) {
                toastr.error('Ошибка при отправке данных на сервер!');
                console.log(err.responseText);
            }
        });
    });

    $('#btn-autoplay').click(function() {
        var enabled = parseFloat($(this).attr('data-enabled'));
        if(isNaN(enabled)) enabled = 0;
        switch (enabled) {
            case 1:
                $(this).css('opacity', '1');
                $('#btn-spin').css('opacity', '1');
                $(this).attr('data-enabled', 0);
                autoPlay = false;
                break;
            case 0:
                // if(game || freeSpins) return toastr.error('Дождитесь конца игры!');
                $(this).css('opacity', '0.4');
                $(this).attr('data-enabled', 1);
                $('#btn-spin').css('opacity', '0.4');
                if(!game && !freeSpins) newGame(1);
                autoPlay = true;
                break;
        }
    });

    $('#btn-spin').click(function() {
        if(autoPlay) return toastr.error('Вам необходимо выключить авто режим!');
        if(game || freeSpins) return toastr.error('Дождитесь конца игры!');
        newGame(0);
    });

    function newGame(is_auto) {
        if(game) return toastr.error('Дождитесь конца игры!');
        var lines = parseFloat($('#line').text());
        if(isNaN(lines)) return toastr.error('Не удалось найти число линий!');

        var bet = $('#bet').text();
        if(bet.indexOf('K') != -1) bet = parseFloat(bet.replace('K', '')) * 1000; else bet = parseFloat(bet);
        if(isNaN(bet)) return toastr.error('Не удалось найти сумму ставки!');

        $.ajax({
            url : '/bandit/newGame',
            type : 'post',
            data : {
                bet : bet,
                lines : lines,
                is_auto : is_auto
            },
            success : function(data) {
                console.log(data);
                if(data.success) {
                            updateLines();
                            $('.mycoins_value').text(data.balance);
                            $(".bandit-container").removeClass("show-game1");
                            $(".bandit-container").removeClass("show-game2");
                            $(".bandit-container").removeClass("show-game3");
                            $(".bandit-container").removeClass("show-menu");
                            console.log(data.game.id);
                            gameID = 0;
                            bandit_lines.hide();
                            $('.bandit-line').css('opacity', 1);
                            $('#bandit-lines').show();
                            if(data.freeCount > -1) freeSpins = true; else {
                                 $('#bottom-side').css('opacity', 1);
                                 $('#btn-spin').html('<br>SPIN');
                                 freeSpins = false;
                            }
                            if(data.type == 'free') {
                                $('#bet').text(data.bet);
                                $('#totalbet').text(data.value);
                                $('#line').text(data.linesCount);
                                $('#bottom-side').css('opacity', 0.5);
                                $('#btn-spin').html('<br>FREE (' + data.freeCount2 + ')');
                            } else {
                                freeSpins = false;
                                $('#btn-spin').html('<br>SPIN');
                            }

                            if(showLines != undefined) clearInterval(showLines);
                            bandit_lines.hide();
                            game = true;
                            $('.win_value').text('?');
                            var lines = $('.slot');
                            var time = 1500;
                            for(var i = 0; i < lines.length; i++) {
                                var html = '';
                                for(var u = 0; u < data.lines[i].length; u++) {
                                    if(typeof data.lines[i][u].line_id == 'undefined') {
                                        html += '<div class="slot-element slot-element-liner" style="background-image: url('+ data.lines[i][u].url +')"></div>';
                                    } else {
                                        var className = '';
                                        for(var o = 0; o < data.lines[i][u].line_id.length; o++) className += 'slot-active-line-' + data.lines[i][u].line_id[o] + ' ';
                                        html += '<div class="slot-element slot-element-liner ' + className + '" style="background-image: url('+ data.lines[i][u].url +')"></div>';
                                    }
                                }
                                $(lines[i]).html(html);
                                var multiplier = (i+1)*2;
                                $(lines[i]).animate({scrollTop:$(lines[i]).height()*multiplier },time, 'linear');
                                if(!$('#sound-btn').hasClass('mute')) banditStartSound.play();
                                time += 1100;
                            }
                            linesIDs = [];
                            for(var i = 0; i < data.winners.length; i++) linesIDs.push(data.winners[i].line);

                            var timeToInterval = time;
                            if(timeToInterval < 0) timeToInterval = 0;
                            setTimeout(function() {
                                banditStartSound.pause();
                                banditStartSound.currentTime = 0;
                                $('.win_value').text(data.win);
                                $('.mycoins_value').text(data.newbalance);
                            }, timeToInterval);
                            if(linesIDs.length > 0) {
                                setTimeout(function() {
                                    showLines = setInterval(function() {
                                        for(var i = 0; i < bandit_lines.length; i++) $(bandit_lines[i]).hide();
                                        if(typeof linesIDs[lineID] == 'undefined') {
                                            clearInterval(showLines);
                                            lineID = 0;
                                            if(data.game.id < 1) {
                                                game = false;
                                                if(freeSpins) {
                                                    $('#btn-spin').html('<br>FREE (' + data.freeCount + ')');
                                                    newGame(0);
                                                    return;
                                                } else if(autoPlay) {
                                                    newGame(1);
                                                    $('#bottom-side').css('opacity', 1);
                                                    $('#btn-spin').html('<br>SPIN');
                                                    return;
                                                } else {
                                                    $('#bottom-side').css('opacity', 1);
                                                    $('#btn-spin').html('<br>SPIN');
                                                }
                                            } else if(data.game.id > 0) {
                                                setTimeout(function() {
                                                    clearInterval(showLines);
                                                    $('#bandit-lines').hide();
                                                    // game = true;
                                                    $('#bottom-side').hide();
                                                    if(data.game.id == 1) {
                                                        $('.questGame_text').text(BONUS_1);
                                                        var list = $('.slot');
                                                        for(var i = 0; i < list.length; i++) {
                                                            var html = '';
                                                            for(var x = 0; x < 3; x++) html += '<div class="slot-element slot-element-liner" style="background-image: url('+ data.game.lines[i][x].url +')"></div>';
                                                            $(list[i]).html(html);
                                                        }
                                                        if(!$(".bandit-container").hasClass("show-game1")) $(".bandit-container").addClass("show-game1");
                                                        setTimeout(function() {
                                                            showGame(data.game.id);
                                                            console.log(data.game.id);
                                                        }, 4500);
                                                    } else if(data.game.id > 1) {
                                                        showGame(data.game.id);
                                                        console.log(data.game.id);
                                                    }
                                                }, 2000);
                                            }
                                        }
                                        for(var i = 0; i < bandit_lines.length; i++) if($(bandit_lines[i]).attr('data-line') == linesIDs[lineID]) $(bandit_lines[i]).show();
                                        $('.slot-element-liner').css('opacity', 0.4);
                                        $('.slot-active-line-' + [linesIDs[lineID]]).css('opacity', 1);
                                        var lines = $('.slot');
                                        for(var i = 0; i < lines.length; i++) {
                                            var multiplier = (i+1)*2;
                                            $(lines[i]).animate({scrollTop:$(lines[i]).height()*multiplier }, 0, 'linear');
                                        }
                                        lineID++;
                                    }, 1000);
                                }, timeToInterval);
                            } else {
                                setTimeout(function() {
                                    game = false;
                                    if(freeSpins) {
                                        $('#btn-spin').html('<br>FREE (' + data.freeCount + ')');
                                        newGame(0);
                                        return;
                                    } else if(autoPlay) {
                                        newGame(1);
                                        $('#bottom-side').css('opacity', 1);
                                        $('#btn-spin').html('<br>SPIN');
                                        return;
                                    } else {
                                        $('#bottom-side').css('opacity', 1);
                                        $('#btn-spin').html('<br>SPIN');
                                    }
                                }, timeToInterval);
                            }

                            // setTimeout(function() {
                            //     $('.win_value').text(data.win);
                            //     if(typeof data.newbalance != null) $('#mycoins').text(data.newbalance);
                            //     if(linesIDs.length < 1) {
                            //         if(data.game.id < 1) {
                            //             game = false;
                            //             if(freeSpins) {
                            //                 // $('#btn-spin').html('<br>FREE (' + data.freeCount + ')');
                            //                 setTimeout(function() {
                            //                     newGame();
                            //                 }, 1000);
                            //             } else if(autoPlay) {
                            //                 setTimeout(function() {
                            //                     newGame();
                            //                     $('#bottom-side').css('opacity', 1);
                            //                     $('#btn-spin').html('<br>SPIN');
                            //                 }, 1000);
                            //             } else {
                            //                 $('#bottom-side').css('opacity', 1);
                            //                 $('#btn-spin').html('<br>SPIN');
                            //             }
                            //         } else if(data.game.id > 0) {
                            //             setTimeout(function() {
                            //                 clearInterval(showLines);
                            //                 $('#bandit-lines').hide();
                            //                 // game = true;
                            //                 $('#bottom-side').hide();
                            //                 if(data.game.id == 1) {
                            //                     var list = $('.slot');
                            //                     for(var i = 0; i < list.length; i++) {
                            //                         var html = '';
                            //                         for(var x = 0; x < 3; x++) html += '<div class="slot-element slot-element-liner" style="background-image: url('+ data.game.lines[i][x].url +')"></div>';
                            //                         $(list[i]).html(html);
                            //                     }
                            //                     if(!$(".bandit-container").hasClass("show-game1")) $(".bandit-container").addClass("show-game1");
                            //                     setTimeout(function() {
                            //                         showGame(data.game.id);
                            //                     }, 4500);
                            //                 } else if(data.game.id > 1) {
                            //                     showGame(data.game.id);
                            //                 }
                            //             }, 1500);
                            //         }
                            //     } else if(data.game.id > 0) {
                            //         setTimeout(function() {
                            //             clearInterval(showLines);
                            //             $('#bandit-lines').hide();
                            //             game = true;
                            //             $('#bottom-side').hide();
                            //             if(data.game.id == 1) {
                            //                 var list = $('.slot');
                            //                 for(var i = 0; i < list.length; i++) {
                            //                     var html = '';
                            //                     for(var x = 0; x < 3; x++) html += '<div class="slot-element slot-element-liner" style="background-image: url('+ data.game.lines[i][x].url +')"></div>';
                            //                     $(list[i]).html(html);
                            //                 }
                            //                 if(!$(".bandit-container").hasClass("show-game1")) $(".bandit-container").addClass("show-game1");
                            //                 setTimeout(function() {
                            //                     showGame(data.game.id);
                            //                 }, 4500);
                            //             } else if(data.game.id > 1) {
                            //                 showGame(data.game.id);
                            //             }
                            //         }, 1500);
                            //     }
                            // }, timeToInterval);
                } else {
                    toastr.error(data.msg);
                    $('#btn-autoplay').attr('data-enabled', 0);
                    $('#btn-autoplay').css('opacity', '1');
                    $('#btn-spin').css('opacity', '1');
                }
            },
            error : function(err) {
                $('#btn-autoplay').attr('data-enabled', 0)
                $('#btn-autoplay').css('opacity', '1');
                $('#btn-spin').css('opacity', '1');
                toastr.error('Ошибка при отправке данных на сервер!');
                console.log(err.responseText);
            }
        });
    }

    var gameOn = false;

    function showGame(n)
    {
        if(gameOn) return;
        gameOn = true;
        $(".bandit-container").addClass("bandit-shuffle");
        gameID = n;
        console.log(gameID);
        $('.questGame_text').text(BONUS_1);
        $('.mGame_text').text(BONUS_2);
        $('.towers_text').text(BONUS_3);
        switch (n) {
            case 1:
            //? QUEST GAME
                $('.slot-element').removeClass('questGame');
                var list = $('.slot-element');
                for(var i = 0; i < list.length; i++) {
                    if(typeof list[i] != 'undefined') {
                        rotateElement($(list[i]), 'http://dotaregal.com/assets/frontend/images/slot/bonus/none.png', 'questGame');
                        // $(list[i]).addClass('questGame');
                        $(list[i]).attr('data-used', 0);
                        $(list[i]).css('opacity', 1);
                    }
                }
                $('.slot-element').css('opacity', 1);
                $('.slot-element').click(function(e) {
                    if(!$(this).hasClass('questGame')) return;
                    var used = parseFloat($(this).attr('data-user'));
                    // if(used) return toastr.error('Вы уже использовали этот итем!');
                    if(used) return;
                    $.ajax({
                        url : '/bandit/quest',
                        type : 'post',
                        success : function(data) {
                            console.log(data.status);
                            if(data.success) {
                                $(e.currentTarget).attr('data-used', 1);
                                rotateElement($(e.currentTarget), data.data.url);
                                setTimeout(function() {
                                    $('.win_value').text(data.data.value);
                                    $('.mycoins_value').text(data.data.balance);
                                }, 1000);
                                switch (data.status) {
                                    case 'allowed':
                                        setTimeout(function() {
                                            $('.questGame_text').text(WIN + ' : ' + data.data.value + ' (x' + data.data.multiplier + ')');
                                        }, 1000);
                                        setTimeout(function() {
                                            $(".bandit-container").addClass("show-menu");
                                        }, 1500);
                                        game = true;
                                        break;
                                    case 'quit' :
                                        setTimeout(function() {
                                            $('.questGame_text').text('LOSE');
                                        }, 1000);
                                        setTimeout(function() {
                                            $(".bandit-container").removeClass("bandit-shuffle");
                                            $(".bandit-container").removeClass("show-game1");
                                            updateLines();
                                            game = false;
                                            gameOn = false;
                                            $('#bottom-side').show();
                                            if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
                                        }, 3000);
                                        break;
                                    case 'spins' :
                                        setTimeout(function() {
                                            $('.questGame_text').text('FREE SPINS!!!');
                                        }, 1000);
                                        setTimeout(function() {
                                            $(".bandit-container").removeClass("bandit-shuffle");
                                            $(".bandit-container").removeClass("show-game1");
                                            updateLines();
                                            game = false;
                                            gameOn = false;
                                            $('#bottom-side').show();
                                            newGame(0);
                                        }, 3000);
                                        break;

                                }
                            } else {
                                if(typeof data.msg != 'undefined') {
                                    toastr.error(data.msg);
                                } else {
                                    toastr.error('Ошибка!');
                                }
                            }
                        },
                        error: function(err) {
                            toastr.error('Ошибка при отправке данных на сервер!');
                            console.log(err.responseText);
                        }
                    });
                });

                setTimeout(function() {
                    shuffle();
                }, 1100);
                break;
            case 2:
            //? MGAME
                $('.slot-element').removeClass('mGame');
                var list = $('.slot-element');
                for(var i = 0; i < list.length; i++) {
                    if(typeof list[i] != 'undefined') {
                        rotateElement($(list[i]), 'http://dotaregal.com/assets/frontend/images/slot/bonus/none.png', 'mGame');
                        $(list[i]).attr('data-used', 0);
                        $(list[i]).css('opacity', 1);
                    }
                }
                $('.slot-element').css('opacity', 1);

                $('.slot-element').click(function(e) {
                    if(!$(this).hasClass('mGame')) return;
                    var used = parseFloat($(this).attr('data-user'));
                    if(used) return toastr.error('Вы уже использовали этот итем!');

                    $.ajax({
                        url : '/bandit/mgame',
                        type : 'post',
                        success : function(data) {
                            console.log(data);
                            if(data.success) {
                                $(e.currentTarget).attr('data-used', 1);
                                rotateElement($(e.currentTarget), data.data.url);
                                setTimeout(function() {
                                    $('.win_value').text(data.data.value);
                                    $('.mycoins_value').text(data.data.balance);
                                    $('.mGame_text').text(WIN + ' : ' + data.data.value + ' (x'+ data.data.multiplier +')');
                                }, 1000);

                                setTimeout(function() {
                                    $(".bandit-container").removeClass("bandit-shuffle");
                                    $(".bandit-container").removeClass("show-game2");
                                    $('#bottom-side').show();
                                    updateLines();
                                    game = false;
                                    gameOn = false;
                                    if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
                                }, 3000);
                            } else {
                                if(typeof data.msg != 'undefined') {
                                    toastr.error(data.msg);
                                } else {
                                    toastr.error('Ошибка!');
                                }
                            }
                        },
                        error: function(err) {
                            toastr.error('Ошибка при отправке данных на сервер!');
                            console.log(err.responseText);
                        }
                    });
                });
                setTimeout(function() {
                    shuffle();
                }, 1100);

                $(".bandit-container").addClass("show-game2");
                break;
            case 3 :
            //? TOWERS
                $('.slot').hide();
                towersStatus = true;
                $('.totem1_m').hide();
                $('.totem2_m').hide();
                $('.totem_event').removeClass('active');
                $(".bandit-container").addClass("show-game3");
                $('#bottom-side').hide();
                game = false;
                break;
        }
    }

    $('.totem_event').click(function() {
        $('.towersStarts').attr('data-tower', $(this).attr('data-tower'));
        $(this).addClass('active');
        if(!$(".menu3").hasClass("show-start")) $(".menu3").addClass("show-start");
        if(!$(".bandit-container").hasClass('show-menu')) $(".bandit-container").addClass("show-menu");
    });

    $('.towersStarts').click(function() {
        var tower = parseFloat($(this).attr('data-tower'));
        if(game) return toastr.error('Дождитесь конца игры!');
        if($(this).attr('data-tower') == '0') return toastr.error('Выберите башню!');
        $.ajax({
            url : '/bandit/towers',
            type : 'post',
            data : {
                tower : tower
            },
            success : function(data) {
                if(data.success) {
                    $('.totem_event').removeClass('active');
                    $(".bandit-container").removeClass("show-menu");
                    $('.totem_event').attr('data-used', 1);
                    $('.totem_m').text('x0');
                    $('.totem' + tower + '_m').text('x2');
                    $('.totem_m').show();

                    rotateselector(data.data.rotate);

                    if(tower != data.data.tower) towersStatus = false; else towersStatus = true;
                    console.log(towersStatus);

                    setTimeout(function() {
                        $('.mycoins_value').text(data.data.balance);
                        $('.win_value').text(data.data.win);
                        $('.totem' + data.data.tower).addClass('active');
                        if(tower == data.data.tower) $('.towers_text').text(WIN + ' :' + data.data.win + '(x2)'); else $('.towers_text').text('LOSE');
                        setTimeout(function() {
                            $('.totem_m').hide();

                            if(tower != data.data.tower) {
                                $(".bandit-container").removeClass("bandit-shuffle");
                                game = false;
                                $('.slot').show();
                                $(".bandit-container").removeClass("show-game3");
                                $('#bottom-side').show();
                                updateLines();
                                rotateselector(90);
                                gameOn = false;
                                if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
                            } else {
                                rotateselector(90);
                                $(".menu3").removeClass("show-start");
                                $(".bandit-container").addClass("show-menu");
                                $('.totem_event').removeClass('disable');
                                $('.totem_event').removeClass('active');
                            }
                        }, 500);
                    }, 3000);
                } else {
                    if(typeof data.msg != 'undefined') {
                        toastr.error(data.msg);
                        if(data.status != 'undefined' && data.status == 'end') {
                            $('.slot').show();
                            updateLines();
                            $(".bandit-container").removeClass("show-game3");
                            $(".bandit-container").removeClass("show-menu");
                            $(".menu3").removeClass("show-start");
                            $('#bottom-side').show();
                            game = false;
                            if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
                        }
                    } else {
                        toastr.error('Ошибка!');
                    }
                    game = false;
                }
            },
            error : function(err) {
                toastr.error('Ошибка при отправке данных на сервер!');
                console.log(err.responseText);
            }
        });
    });

    $('.CONTINUE_GAME').click(function() {
        if(gameID == 3) {
            $(".bandit-container").removeClass("show-menu");
            $(".menu3").addClass("show-start");
        } else {
            $(".bandit-container").removeClass("show-menu");
        }
        $('.questGame_text').text(BONUS_1);
        $('.mGame_text').text(BONUS_2);
        $('.towers_text').text(BONUS_3);
    });

    $('.FINISH_GAME').click(function() {
        gameOn = false;
        $(".bandit-container").removeClass("bandit-shuffle");
        if(gameID == 3) {
            $('.slot').show();
            updateLines();
            $(".bandit-container").removeClass("show-game3");
            $(".bandit-container").removeClass("show-menu");
            $(".menu3").removeClass("show-start");
            $('#bottom-side').show();
            game = false;
            if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
        } else {
            $(".bandit-container").removeClass("show-game1");
            $(".bandit-container").removeClass("show-game2");
            $(".bandit-container").removeClass("show-game3");
            $(".bandit-container").removeClass("show-menu");
            updateLines();
            $('#bottom-side').show();
            game = false;
            if(freeSpins) newGame(0); else if(autoPlay) newGame(1);
        }
        $.ajax({
            url : '/bandit/finish_game',
            type : 'post',
            error: function(err)
            {
                console.log(err.responseText);
            }
        });
    });

    var lineCleaner;

    function showLinesByCount(count) {
        bandit_lines.hide();
        $('.bandit-line').css('opacity', 1);
        $('#bandit-lines').show();
        clearTimeout(lineCleaner);
        $(bandit_lines).hide();
        for(var i = 0; i < count; i++) for(var u = 0; u < bandit_lines.length; u++) {
            if($(bandit_lines[u]).attr('data-line') == (i+1)) $(bandit_lines[u]).show();
            setTimeout
        }
        lineCleaner = setTimeout(function() {
            $(bandit_lines).hide();
        }, 2000);
    }

    $('body').keypress(function(e) {
        if(e.which == 13) newGame(0);
    });


});
