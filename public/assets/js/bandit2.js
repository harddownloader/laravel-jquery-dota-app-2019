$(document).ready(function() {

    var socket = io.connect(':8443', {secure:true});

    $('#addBet').click(function() {
        var value = parseFloat($('#value').val());
        if(isNaN(value)) return toastr.error('Вы забыли указать сумму ставки!');

        $.ajax({
            url : '/bandit/newGame',
            type : 'post',
            data : {
                value : value
            },
            success : function(data) {
                console.log(data);
                if(data.success) {
                    var lines = $('.line');
                    for(var i = 0; i < lines.length; i++) {
                        $(lines[i]).find('.items').css('margin-top', '0');
                        $(lines[i]).find('.items').html('');
                        for(var u = 0; u < data.lines[i].length; u++) {
                            $(lines[i]).find('.items').append('<div class="item" data-i="'+u+'"><img src="'+data.lines[i][u].url+'" width="198px" height="198px"></div>');
                        }
                        $(lines[i]).find('.items').animate({'margin-top' : -199*79}, data.time*1000, function() {
                            if(data.multiplier > 0) alert('Поздравляем, вы выиграли ' + data.value + '. Множитель х' + data.multiplier);
                        });
                    }
                    $('#myBet').text(data.value);
                } else {
                    return toastr.error(data.msg);
                }
            },
            error : function(err) {
                console.log(err.responseText);
            }
        });
    });
});
