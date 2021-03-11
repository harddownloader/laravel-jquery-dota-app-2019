$(document).ready(function() {
    $('#chat').animate({scrollTop:$('#messages').height()},400);

    var socket = io.connect('https://dotaregal.com:8443', {secure:true});

    $('#chat-input').keypress(function(e) {
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
        // var room = location.pathname.replace('/', '');
        // if(data.room != room) return;

        console.log(data.room + ' - ' + location.pathname);

        switch (data.room) {
            case 'bandit':
                if((location.pathname.indexOf('paytable') == -1) && (location.pathname != '/bandit')) return;
                break;
            case 'double':
                if(location.pathname != '/double') return;
                break;
            case 'poker':
                if(location.pathname != '/poker') return;
                break;
            default:
                return;
                break;
        }

        console.log(location.pathname);

        var message = '';
            message += '<li class="media">';
            message += '<div class="media-body">';
            message += '<div class="media">';
            message += '<a class="pull-left" href="#">';
            message += '<div class="user-img chat-img" style="background-image: url(' + data.avatar + ')"></div>';
            message += '</a>';
            message += '<div class="media-body">';
            message += '<span class="chat-name" style="cursor : pointer;">' + data.username + '</span>';
            message += '<small> LVL</small><span class="chat-level">' + data.lvl + '</span><br>';
            message += '<small class="chat-message">' + data.message + '</small>';
            message += '</div>';
            message += '</div>';
            message += '</div>';
            message += '</li>';

/*
<li class="media">
    <div class="media-body">
        <div class="media">
            <a class="pull-left" href="#">
                <div class="user-img chat-img" style="background-image: url({{ $message['avatar'] }})"></div>
            </a>
            <div class="media-body">
                <span class="chat-name">{{ $message['username'] }}</span>
                <small>LVL</small><span class="chat-level">{{ $message['lvl'] }}</span><br>
                <small class="chat-message">{{ $message['message'] }}</small>

            </div>
        </div>

    </div>
</li>
*/

        $('#messages').append(message);

        $('#chat').animate({scrollTop:$('#messages').height()},400);

        $('.chat-name').click(function() {
            $('#chat-input').val('@' + $(this).text() + ', ');
            $('#chat-input').focus();
        });
    });

    $('.chat-name').click(function() {
        $('#chat-input').val('@' + $(this).text() + ', ');
        $('#chat-input').focus();
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
