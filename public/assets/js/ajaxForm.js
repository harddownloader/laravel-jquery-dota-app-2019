$(document).ready(function() {
    
    $('form').submit(function(e) {
        if($(this).hasClass('not-auto')) return;
        $.ajax({
            url     : $(this).attr('action'),
            type    : $(this).attr('method'),
            data    : $(this).serialize(),
            success : function(data) {
                if(data.success) {
                    toastr.success(data.msg);
                } else {
                    if(typeof data.msg != 'undefined') {
                        toastr.error(data.msg);
                    } else {
                        toastr.error('Ошибка! Попробуйте чуть позже.');
                    }
                }
            },
            error   : function(err) {
                console.log(err.responseText);
                toastr.error('Ошибка при отправке данных на сервер!');
            }
        })
        e.preventDefault(); 
    });
    
});