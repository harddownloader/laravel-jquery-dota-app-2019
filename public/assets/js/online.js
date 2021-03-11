$(document).ready(function() {
    var socket = io.connect('https://dotaregal.com:8443', {secure:true});

    function getRoom()
    {
    	var url = location.pathname;
    	if(url.indexOf('poker') != -1) return 'poker';
    	if(url.indexOf('bandit') != -1) return 'bandit';
    	if(url.indexOf('double') != -1) return 'double';	
    }

    socket.emit('reg', {
        user_id : USER_ID,
        steamid64 : USER_STEAMID64,
        username : USER_USERNAME,
        avatar : USER_AVATAR,
        room : getRoom()
    });
});
