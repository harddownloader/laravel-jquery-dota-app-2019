$(document).ready(function() {
    var all = NaN;
    var selectedItems = [];
    var page = 1;

    $("#price_input").ionRangeSlider({
        type: "double",
        min: 0,
        max: 2000,
        from: 0,
        to: 2000,
        hide_min_max: true,
        hide_from_to: true,
        grid: false,
        onChange: function (data) {
            $('#price_from').val(data.from);
            $('#price_to').val(data.to);
        }
    });

    var input = $("#price_input").data("ionRangeSlider");

    // var input;

    function getItems()
    {
        $.ajax({
            url : '/shop_parse',
            type : 'post',
            success : function(data) {
                if(!data.success) {
                    toastr.error(data.msg);
                    return;
                }
                all = data.items;
                showAspects(data.aspects);
                $('#all_count').text('(' + all.length + ')');

                $('#price_from').val(data.aspects.min);
                $('#price_to').val(data.aspects.max);

                input.update({
                    min : data.aspects.min,
                    max : data.aspects.max,
                    from : data.aspects.min,
                    to : data.aspects.max
                });

                showItems(data.items);
            },
            error : function(err) {
                toastr.error('Error!');
                console.log(err.responseText);
            }
        });
    }

    getItems();

    function showAspects(data) {
        var heroes      = '<option>All</option>',
            raritys     = '<option>All</option>',
            types       = '<option>All</option>',
            qualitys    = '<option>All</option>';

        data.heroes.sort(function(a, b) {
            if(b < a) return 1;
            if(b > a) return -1;
            return 0
        });

        data.raritys.sort(function(a, b) {
            if(b < a) return 1;
            if(b > a) return -1;
            return 0
        });

        data.types.sort(function(a, b) {
            if(b < a) return 1;
            if(b > a) return -1;
            return 0
        });

        data.qualitys.sort(function(a, b) {
            if(b < a) return 1;
            if(b > a) return -1;
            return 0
        });

        for(var i = 0; i < data.heroes.length; i++)     heroes      += '<option>' + data.heroes[i] + '</option>';
        for(var i = 0; i < data.raritys.length; i++)    raritys     += '<option>' + data.raritys[i] + '</option>';
        for(var i = 0; i < data.types.length; i++)      types       += '<option>' + data.types[i] + '</option>';
        for(var i = 0; i < data.qualitys.length; i++)   qualitys    += '<option>' + data.qualitys[i] + '</option>';

        $('#heroes').html(heroes);
        $('#raritys').html(raritys);
        $('#types').html(types);
        $('#qualitys').html(qualitys);

        $('#price_from').val(data.min);
        $('#price_to').val(data.max);

        input.update({
            from : data.min,
            to : data.max
        });
    }

    function showItems(items)
    {

        // Search
        var list        = [];
        var search      = $('#search').val().toLowerCase();
        var rarity      = $('#raritys').val().toLowerCase();
        var types       = $('#types').val().toLowerCase();
        var heroes      = $('#heroes').val().toLowerCase();
        var qualitys    = $('#qualitys').val().toLowerCase();
        var from        = parseFloat($('#price_from').val());
        var to          = parseFloat($('#price_to').val());

        var key = 0;
        for(var i = 0; i < items.length; i++) {
            if(typeof items[i] != 'undefined') {
                var u = 0;
                if(search == '' || (items[i].market_hash_name.toLowerCase().indexOf(search) != -1)) u++;
                if((rarity == 'all') || (items[i].rarity.toLowerCase() == rarity)) u++;
                if((types == 'all') || (items[i].type.toLowerCase() == types)) u++;
                if((heroes == 'all') || (items[i].hero.toLowerCase() == heroes)) u++;
                if((qualitys == 'all') || (items[i].quality.toLowerCase() == qualitys)) u++;
                if(items[i].price <= to && items[i].price >= from) u++;


                if(u == 6) {
                    list[key] = items[i];
                    list[key].i = i;
                    key++;
                }
            }
        }

        if(list.length == 0) {
            $('#shop-container').html('Мы не смогли найти вещи, подходящие вашему запросу...');
            showPages(0);
            return;
        }

        switch ($('#sort').val()) {
            case 'asc':
                list.sort(function(a, b) {
                    if(a.price < b.price) return -1;
                    if(a.price > b.price) return 1;
                    return 0;
                });
                break;
            case 'desc':
                list.sort(function(a, b) {
                    if(a.price < b.price) return 1;
                    if(a.price > b.price) return -1;
                    return 0;
                });
                break;
        }


        // Insert
        var html = '';
        if(list.length < (48*page)) page = 1;

        for(var x = (48*(page-1)); x < (48*page); x++) {
            if(typeof list[x] != 'undefined' && list[x].count > 0) {
                // x++;
                html += '<div class="col-xs-12 col-sm-6 col-md-2 shop-item t-spacing" data-id="' + list[x].i + '" style="height : 190px;">';
                html += '<div class="img-scale"><img src="http://cdn.steamcommunity.com/economy/image/' + list[x].icon_url + '/180fx120f" class="img-responsive bottom-space5"></div>';
                html += '<span class="item-name">' + list[x].market_hash_name + '</span><br>';
                html += '<span class="item-type bottom-space5">' + list[x].type + '</span><br>';
                html += '<span class="item-price">' + list[x].price + '</span><div class="coin noselect"></div>';
                if(list[x].count > 1) {
                    html += '<div class="quantity">';
                    html += '<button class="substraction">-</button>';
                    html += '<input type="number" class="count_value" value="' + list[x].count + '" style="width: 55px;">';
                    html += '<button class="addition">+</button>';
                }
                html += '<br><button class="addSelection" style="color : #000;margin-top : 10px;width: 55px;height: 17px;font-size: 8px;background: #eee;text-align: center;margin-left: -4px;">Добавить</button>'
                if(list[x].count > 1) html += '</div>';
                html += '</div>';
            }
        }
        $('#shop-container').html(html);
        $('#all_count').text('(' + updateCount(list) + ')');

        html = '';
        var cost = 0;
        var count = 0;
        for(var i = 0; i < selectedItems.length; i++) {
            if(typeof selectedItems[i] != 'undefined') {
                var text = '';
                if(selectedItems[i].count > 1) text = '(' + selectedItems[i].count + ') ';
                html += '<div class="col-xs-10 col-xs-offset-1 shop-item t-spacing shop-item selected_item addSelection" data-id="' + i + '">';
                html += '<img src="http://cdn.steamcommunity.com/economy/image/' + selectedItems[i].icon_url + '/180fx120f" class="img-responsive bottom-space5">';
                html += '<span class="item-name">' + selectedItems[i].market_hash_name + '</span><br>';
                html += '<span class="item-type bottom-space5">' + selectedItems[i].type + '</span><br>';
                html += '<span class="item-price">' + text + '' + selectedItems[i].price + '</span><div class="coin noselect"></div>';
                html += '</div>';

                cost += selectedItems[i].price;
                count += selectedItems[i].count;
            }
        }

        $('#selected_items').html(html);
        $('#cost-coins').text(cost);
        $('#count').text(count);

        // updateCount();

        showPages(updateCount(list));

        itemClick();

    }

    $('.search_button').click(function() {
        getSearch();
        page = 1;
    });

    $('body').keypress(function(e) {
        if(e.which == 13) getSearch();
    });

    function updateCount(array)
    {
        var c = 0;
        for(var i = 0; i < array.length; i++) if(typeof array[i] != 'undefined') c++;
        return c;
    }

    function getSearch() {
        var from = parseFloat($('#price_from').val());
        if(isNaN(from)) from = 0;
        var to = parseFloat($('#price_to').val());
        if(isNaN(to)) to = 0;

        $('#price_from').val(from);
        $('#price_to').val(to);

        input.update({
            from : from,
            to : to
        });

        showItems(all);
    }

    function itemClick() {
        $('.substraction').click(function() {
            var id = parseFloat($(this).parent().parent().attr('data-id'));
            var value = parseFloat($(this).parent().find('.count_value').val());
            value--;
            if(value < 0) return;
            $(this).parent().find('.count_value').val(value);
        });

        $('.addition').click(function() {
            var id = parseFloat($(this).parent().parent().attr('data-id'));
            var value = parseFloat($(this).parent().find('.count_value').val());
            value++;
            if(value > all[id].count) return toastr.error('Максимально кол-во - ' + all[id].count);
            $(this).parent().find('.count_value').val(value);
        });

        $('.addSelection').click(function() {
            var id = parseFloat($(this).parent().attr('data-id'));
            if(isNaN(id)) id = parseFloat($(this).parent().parent().attr('data-id'));
            if(isNaN(id)) id = parseFloat($(this).attr('data-id'));
            if(isNaN(id) && id < 0) return;
            if($(this).hasClass('selected_item')) {
                all[id].count += selectedItems[id].count;
                delete selectedItems[id];
            } else {
                var itemsCount;
                if(all[id].count > 1) itemsCount = parseFloat($(this).parent().find('.count_value').val()); else itemsCount = 1;

                if(itemsCount > all[id].count) return toastr.error('Максимально кол-во - ' + all[id].count);

                all[id].count = all[id].count - itemsCount;
                selectedItems[id] = {
                    market_hash_name : all[id].market_hash_name,
                    classid : all[id].classid,
                    icon_url : all[id].icon_url,
                    bot_id : all[id].bot_id,
                    id : all[id].id,
                    price : all[id].price*itemsCount,
                    count : itemsCount
                };
                // selectedItems[id].count = itemsCount;
                // selectedItems[id].price = selectedItems[id].price*itemsCount;
                console.log(all[id]);
            }

            showItems(all);
        });
    };

    $('.sendOffer').click(function() {
        var list = [];
        for(var i = 0; i < selectedItems.length; i++) if(typeof selectedItems[i] != 'undefined') list.push({
            classid : selectedItems[i].classid,
            count : selectedItems[i].count,
            market_hash_name : selectedItems[i].market_hash_name
        });

        if(list.length < 1) return toastr.error('Вы забыли выбрать предметы!');

        $.ajax({
            url : '/shop_send',
            type : 'post',
            data : {
                items : list
            },
            success : function(data) {
                console.log(data);
                if(!data.success) return toastr.error(data.msg);
                toastr.success(data.msg);
                selectedItems = [];
                $('#selected_items').html('');
                $('#cost-coins').text(0);
                $('#count').text(0);

                if(typeof data.balance != 'undefined') $('.balance').text(data.balance);
            },
            error : function(err) {
                toastr.error('Ошибка!');
                console.log(err.responseText);
            }
        });

        // toastr.success('Собираем трейд...');
    });

    function pageClick() {
        $('.page').click(function(e) {
            var type = $(this).attr('data-page');
            if(type == 'prev') {
                page--;
                if(page < 1) page = 1;
            } else if(type == 'next') {
                page++;
                if(page > Math.floor(all.length/48)) page = Math.floor(all.length/48);
            } else {
                page = parseFloat(type);
                if(page < 1) page = 1;
                if(page > Math.floor(all.length/48)) page = Math.floor(all.length/48);
            }

            showItems(all);
        });
    }

    function showPages(length) {

        length = Math.floor(length/48);

        var min_page = page-5,
            max_page = page+4;

        if(page < 7) {
            min_page = 1;
            if(length >= 10) max_page = 10; else max_page = length;
        } else if(page == length || max_page > length) {
            max_page = length;
            min_page = length-9;
        }

        var array = [];


        var html = '';
        if(page > 1) html += '<a class="page" data-page="prev">PREV</a>';
        for(var i = min_page; i < max_page+1; i++) {
            if(i == page) html += '<a class="page active" data-page="' + i + '" style="border : 1px solid #c3bcbb;">' + i + '</a>'; else html += '<a class="page" data-page="' + i + '">' + i + '</a>';

        }
        if(page < length) html += '<a class="page" data-page="next">NEXT</a>';

        $('#pagination').html(html);
        $('#pagination').find('a').css('cursor', 'pointer');

        pageClick();
    }

    $('.removeAllItems').click(function() {
        $('#selected_items').html('');
        $('#cost-coins').text('0');
        $('#count').text('0');
        for(var i = 0; i < selectedItems.length; i++) if(typeof selectedItems[i] != 'undefined') {
            all[i] = selectedItems[i];
            delete selectedItems[i];
        }
        showItems(all);     
    });

});
