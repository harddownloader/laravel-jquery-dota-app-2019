$(document).ready(function() {
    $('.messages').animate({scrollTop:1000000}, 400);

    var socket = io.connect(':8443', {secure:true});

    $('#message').keypress(function(e) {
        if(e.which == 13) {
            $.ajax({
                url  : '/chat/send',
                type : 'post',
                data : {
                    room    : location.pathname,
                    message : $(this).val()
                },
                success : function(data) {
                    if(data.success) {
                        toastr.success(data.msg);
                    } else {
                        if(typeof data.msg != 'undefined') {
                            toastr.error(data.msg);
                        } else {
                            toastr.error('Ошибка! Попробуйте чуть позже!');
                        }
                    }
                },
                error   : function(data) {
                    console.log(data.responseText);
                    toastr.error('Ошибка! Попробуйте чуть позже!');
                }
            });
            $(this).val('');
            return false;
        }
    });

    socket.on('chat.new.msg', function(data) {
        if(data.room != location.pathname.replace('/', '')) return;

        var message = '';
            message += '<div class="message">';
            message += '<div class="avatar">';
            message += '<img src="' + data.avatar + '">';
            message += '</div>';
            message += '<div class="msg">';
            message += '<div class="username">' + data.username + '</div>';
            message += data.message;
            message += '</div>';
            message += '</div>';
            message += '<br><br><br>';

        $('.messages').append(message);

        $('.messages').animate({scrollTop:1000000}, 400);
    });

/*
<li class="media">
    <div class="media-body">
        <div class="media">
            <a class="pull-left" href="#">
                <img class="chat-img media-object img-circle "
                src="https://raw.github.com/eladnava/material-letter-icons/master/dist/png/A.png">
            </a>
            <div class="media-body">
                <span class="chat-name">Unknown</span><br>
                <small class="chat-message">{{ $message->message }}</small>

            </div>
        </div>

    </div>
</li>
*/

});
