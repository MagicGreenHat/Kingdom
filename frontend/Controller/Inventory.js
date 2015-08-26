/**
 * Открытие и отображение блока инвентаря персонажа
 */
$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');
    var $openButton = $('.open-inventory-button');

    $openButton.click(function () {
        $inventory.show();
        $room.hide();
        $userInfo.hide();
    });

    $inventory.find('.close-button').click(function () {
        $room.show();
        $userInfo.hide();
        $inventory.hide();
    });

    $openButton.on('click', function () {
        var html = '<div>Золото: ' + Kingdom.Money.getGold() + '</div>'
            + '<div>Серебро: ' + Kingdom.Money.getSilver() + '</div>';

        $('#game-inventory .money').html(html);
    });

    /**
     * Отрисовка инвентаря
     */
    function renderInventory() {
        var html = '';
        var inventory = Kingdom.Inventory.getItems();

        inventory.done(function () {
            var $paperdoll = $('#game-inventory .paperdoll');
            var imagePath = '/img/items/';
            var imageExtension = '.png';

            inventory.items.forEach(function (item) {
                if (item.slot) {
                    var $slot = $paperdoll.find('.' + item.slot + '.slot img');
                    $slot.attr('src', imagePath + item.pic + imageExtension);
                } else {
                    html += '<div class="item" ' +
                        'data-name="' + item.name + '" ' +
                        'data-description="' + item.description + '" ' +
                        'data-slots="' + item.allowedSlots + '">';

                    html += '<img src="' + imagePath + item.pic + imageExtension + '">';

                    if (item.quantity > 1) {
                        html += '<span class="quantity">' + item.quantity + '</span>';
                    }

                    html += '</div>';
                }
            });

            $('#game-inventory .items-list').html(html);

            $('#game-inventory .items-list .item').each(function (key, itemElement) {
                var $item = $(itemElement);
                var name = $item.data('name');
                var $text = $('<div>').html(renderInfoText($item));

                $item.qtip({
                    content: {
                        title: name,
                        text: $text
                    },
                    position: {
                        target: 'mouse',
                        adjust: { x: 5, y: 5 }
                    },
                    style: { classes: 'qtip-items' }
                });
            });
        });
    }

    function renderInfoText($item) {
        var description = $item.data('description');
        var slots = $item.data('slots');
        var infoText = '';

        if (description != '') {
            infoText += description + '<br><br>';
        }

        infoText += '<strong>Можно одеть:</strong> ' + slots + '<br>';

        return infoText;
    }

    renderInventory();
});
