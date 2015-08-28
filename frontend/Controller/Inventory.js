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
                var itemPicture = imagePath + item.pic + imageExtension;

                if (item.slot) {
                    var $slot = $paperdoll.find('.' + item.slot + '.slot');

                    $slot.addClass('dressed');

                    $slot.data('id', item.itemId);
                    $slot.data('name', item.name);
                    $slot.data('description', item.description);
                    $slot.data('slots', item.allowedSlots.join());

                    $slot.find('img').attr('src', itemPicture);
                } else {
                    html += '<div class="item ' + item.allowedSlots.join(' ') + '" ' +
                        'data-id="' + item.itemId + '" ' +
                        'data-name="' + item.name + '" ' +
                        'data-description="' + item.description + '" ' +
                        'data-slots="' + item.allowedSlots + '">';

                    html += '<img src="' + itemPicture + '">';

                    if (item.quantity > 1) {
                        html += '<span class="quantity">' + item.quantity + '</span>';
                    }

                    html += '</div>';
                }
            });

            $('#game-inventory .items-list').html(html);

            makeItemsDraggable();
        });
    }

    /**
     * Настройка draggable-предметов
     */
    function makeItemsDraggable() {
        $('#game-inventory .items-list .item').add($('#game-inventory .paperdoll .slot.dressed')).each(function (key, itemElement) {
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
                    adjust: {x: 10, y: 10}
                },
                style: {
                    classes: 'qtip-items',
                    tip: {
                        corner: false
                    }
                }
            });
        });

        $('#game-inventory .items-list .item').draggable({
            stack: '.item',
            containment: '#game-inventory',
            scroll: false,
            revert: 'invalid'
        });
    }

    /**
     * Отрисовка контента всплывающего окна с информацией о предмете
     * @param $item
     * @returns string
     */
    function renderInfoText($item) {
        var description = $item.data('description');
        var slots = $item.data('slots').split(',');

        var infoText = '';
        if (description != '') {
            infoText += description + '<br><br>';
        }

        infoText += '<strong>Можно одеть:</strong> ';

        var slotNames = [];
        slots.forEach(function (slotName) {
            if (slotName == 'ring_first' || slotName == 'ring_second') {
                slotName = 'ring';
            }

            var translatedSlotName = translateItemName(slotName);

            if($.inArray(translatedSlotName, slotNames) === -1) {
                slotNames.push(translatedSlotName);
            }
        });

        infoText += slotNames.join(', ') + '<br>';

        return infoText;
    }

    /**
     * Перевод типа слота на русский
     * @param itemName
     * @returns string
     */
    function translateItemName(itemName) {
        var names = {
            head: 'на голову',
            amulet: 'как амулет',
            body: 'на тело',
            cloak: 'как плащ',
            weapon: 'как оружие',
            left_hand: 'как щит',
            gloves: 'на руки',
            ring: 'на палец',
            legs: 'на ноги',
            boots: 'как обувь'
        };

        return names[itemName];
    }

    /**
     * Настройка droppable-"куклы" персонажа
     */
    function initializePaperdollSlots() {
        $('#game-inventory .paperdoll .slot').each(function (key, element) {
            var $slot = $(element);
            var slotName = $slot.data('slot');

            $slot.droppable({
                accept: '#game-inventory .items-list .item.' + slotName,
                activeClass: 'highlight',
                drop: function (event, ui) {
                    var $item = $(ui.draggable);
                    var $itemQtip = $item.qtip('api');

                    $itemQtip.destroy();

                    $slot.qtip({
                        content: $itemQtip.get('content'),
                        position: $itemQtip.get('position'),
                        style: $itemQtip.get('style')
                    });

                    $slot.find('img').attr('src', $item.find('img').attr('src'));
                    $item.remove();

                    Kingdom.Websocket.command('wear', [$item.data('id'), slotName])
                }
            });
        });
    }

    // Запуск команд
    initializePaperdollSlots();
    renderInventory();

});
