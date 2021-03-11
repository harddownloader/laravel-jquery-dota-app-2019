$(document).ready(function() {

    var trips = 0,
        ante = 0,
        blind = 0;

    var value = NaN;

    $('.poker_change_value').click(function() {
        value = parseFloat($(this).attr('data-value'));
        $('.poker_change_value').css('border', '');
        $(this).css('border', '1px solid #fff');
        if(isNaN(value)) return toastr.error('Ошибка. Попробуйте чуть позже!');
    });

    $('.poker-trips').click(function() {
        if(isNaN(value)) return toastr.error('Ошибка. Попробуйте чуть позже!');
        trips += value;
        $(this).html('<div class="chip-img chip-25"></div>&nbsp;');
        updateBet();
    });

    $('.poker-ante').click(function() {
        if(isNaN(value)) return toastr.error('Ошибка. Попробуйте чуть позже!');
        trips += value;
        $(this).html('<div class="chip-img chip-25"></div>&nbsp;');
        updateBet();
    });

    function updateBet()
    {
        var sum = trips+ante+blind;
        if(sum != parseFloat(sum.toFixed(0))) sum = sum.toFixed(1);
        $('#bet').text(sum);
    }

});
