$(document).ready(function() {

    $('#promoButton').click(function() {
        $.ajax({
            url : '/promo/redeem',
            type : 'post',
            data : {
                promo : $('#promoCode').val()
            },
            success : function(data) {
                if(data.success) {
                    toastr.success(data.msg);
                    $('#mycoins').text(data.balance);
                } else {
                    if(typeof data.msg != 'undefined') toastr.error(data.msg);
                }
            },
            error : function(err) {
                toastr.error('Ошибка при отправке данных на сервер!!!');
                console.log(err.responseText);
            }
        });
    });

    $('#promoCode').on('keypress', function(e) {
        if(e.which == 13)
        {
            $.ajax({
                url : '/promo/redeem',
                type : 'post',
                data : {
                    promo : $('#promoCode').val()
                },
                success : function(data) {
                    if(data.success) {
                        toastr.success(data.msg);
                        $('#mycoins').text(data.balance);
                    } else {
                        if(typeof data.msg != 'undefined') toastr.error(data.msg);
                    }
                },
                error : function(err) {
                    toastr.error('Ошибка при отправке данных на сервер!!!');
                    console.log(err.responseText);
                }
            });
            e.preventDefault();
        }
    });

    var socket = io.connect('https://dotaregal.com:8443', {secure:true});

    socket.on('message', function(e) {
        if(USER_ID == e.user_id) {
            switch(e.type)
            {
                case 'success':
                    toastr.success(e.msg);
                    break;
                case 'error':
                    toastr.error(e.msg);
                    break;
                case 'info':
                    toastr.info(e.msg);
                    break;
            }
        }
    });

    socket.on('updateBalance', function(e) {
        if(USER_ID == e.user_id) $('.balance').text(e.balance);
    });

    socket.on('lvlup', function(data) {
        if(USER_ID == data.user_id) $('#info-level').text(data.lvl);
    });
});
