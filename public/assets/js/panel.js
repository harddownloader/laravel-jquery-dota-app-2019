$(document).ready(function() {
    var socket = io.connect('https://dotaregal.com:8443', {secure:true});

    socket.emit('getOnline', true);

    socket.on('online', function(data) {
        $('#online_count').text(data.users.length);

        var html = '';
        for(var i = 0; i < data.users.length; i++) {
            html += '<tr>';
            html += '<td><a href="/admin/user_edit/' + data.users[i].user_id + '"><img src="' + data.users[i].avatar + '" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> ' + data.users[i].username + '</a></td>';
            html += '<td>' + data.users[i].user_id + ' / ' + data.users[i].steamid64 + '</td>';
            html += '<td>' + data.users[i].pages + '</td>';
            html += '</tr>';
        }
        $('#online').html(html);

        $('#bandit_count').text(data.rooms.bandit);
        $('#poker_count').text(data.rooms.poker);
        $('#double_count').text(data.rooms.double);
    });
});


/*
<tr>
    <td><img src="{{ $user->avatar }}" alt="" width="30" style="float: left; border-radius : 50%; margin-right : 5px;"> {{ $user->username }} @if($user->flagState) [{{ $user->flagState }}] @endif</td>
    <td>USER_ID / STEAMID64</td>
    <td>PAGES</td>
    <td><a href="{{ $user->trade }}">{{ $user->trade }}</a></td>
</tr>
*/
