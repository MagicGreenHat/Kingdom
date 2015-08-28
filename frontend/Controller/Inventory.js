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

        $('#game-inventory').find('.money').html(html);
    });

    /**
     * Отрисовка инвентаря
     */
    function renderInventory() {
        var html = '';
        var inventory = Kingdom.Inventory.getItems();
        var imagePath = '/img/items/';
        var imageExtension = '.png';

        inventory.done(function () {
            var $inventory = $('#game-inventory');
            var $paperdoll = $inventory.find('.paperdoll');

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

            $inventory.find('.items-list').html(html);

            initializePaperdollSlots();
            renderInventoryInfo($inventory);
            makeInventoryDroppable($inventory);
            makeItemsDraggable($inventory);
        });
    }

    /**
     * Отрисовка всплывающих окон для всех предметов в инвентаре
     * @param $inventory
     */
    function renderInventoryInfo($inventory) {
        $inventory.find('.items-list .item').add($inventory.find('.paperdoll .slot.dressed')).each(function (key, itemElement) {
            var $item = $(itemElement);
            var name = $item.data('name');

            $item.qtip({
                content: {
                    title: name,
                    text: $('<div>').html(renderInfoText($item))
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
     * Настройка draggable-предметов
     */
    function makeItemsDraggable($inventory) {
        $inventory.find('.items-list .item').draggable({
            stack: '.item',
            containment: '#game-inventory',
            scroll: false,
            revert: 'invalid'
        });
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
        $('#game-inventory').find('.paperdoll .slot').each(function (key, element) {
            var $slot = $(element);
            var slotName = $slot.data('slot');

            $slot.droppable({
                accept: '#game-inventory .items-list .item.' + slotName,
                activeClass: 'highlight',
                drop: function (event, ui) {
                    wearItem($(ui.draggable), $slot);
                }
            });

            if ($slot.hasClass('dressed')) {
                var $slotImage = $slot.find('img');

                $slot.draggable({
                    stack: '.item',
                    containment: '#game-inventory',
                    scroll: false,
                    helper: 'clone',
                    start: function (event, ui) {
                        var $draggingItem = $(ui.helper);

                        $slot.addClass('highlight');
                        $slot.data('previous-image', $slotImage.attr('src'));
                        $slotImage.attr('src', $slot.data('img'));
                        $draggingItem.css('background-color', 'transparent');
                        $draggingItem.css('border', 'none');
                    }
                });
            }
        });
    }

    /**
     * Настройка droppable-инвентаря
     * @param $inventory
     */
    function makeInventoryDroppable($inventory) {
        $inventory.find('.items-list, .paperdoll').droppable({
            accept: '#game-inventory .paperdoll .slot',
            drop: function (event, ui) {
                var $slot = $(ui.draggable);
                var $eventTarget = $(event.target);

                if ($eventTarget.hasClass('paperdoll')) {
                    $slot.find('img').attr('src', $slot.data('previous-image'));
                } else {
                    removeItem($slot);
                }

                $slot.removeClass('highlight');
            }
        });
    }

    /**
     * Одеть предмет
     * @param $item
     * @param $slot
     */
    function wearItem($item, $slot) {
        var $itemQtip = $item.qtip('api');
        var itemId = $item.data('id');
        var slotName = $slot.data('slot');

        $itemQtip.destroy();

        $slot.qtip({
            content: $itemQtip.get('content'),
            position: $itemQtip.get('position'),
            style: $itemQtip.get('style')
        });

        $slot.data('id', itemId);
        $slot.find('img').attr('src', $item.find('img').attr('src'));
        $item.remove();

        Kingdom.Websocket.command('wear', [itemId, slotName]);
    }

    /**
     * Снять предмет
     * @param $slot
     */
    function removeItem($slot) {
        var slotName = $slot.data('slot');

        $slot.draggable('destroy');
        $slot.qtip('destroy', true);

        Kingdom.Websocket.command('remove', slotName);
    }

    // Запуск команд
    renderInventory();

});
