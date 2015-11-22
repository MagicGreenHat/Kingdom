/**
 * Открытие и отображение блока инвентаря персонажа
 */
$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');
    var $openButton = $('.open-inventory-button');

    $openButton.click(function () {
        if ($inventory.is(':hidden')) {
            renderInventory();
            $inventory.show();
            $room.hide();
        } else {
            $inventory.hide();
            $room.show();
        }

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
                    $slot.removeClass('nointeract');

                    $slot.data('id', item.itemId);
                    $slot.data('name', item.name);
                    $slot.data('name4', item.name4);
                    $slot.data('description', item.description);
                    $slot.data('slots', item.allowedSlots.join());

                    $slot.find('img').attr('src', itemPicture);
                } else {
                    html += '<div class="item ' + item.allowedSlots.join(' ') + '" ' +
                        'data-id="' + item.itemId + '" ' +
                        'data-name="' + item.name + '" ' +
                        'data-name4="' + item.name4 + '" ' +
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

            renderInventoryInfo($inventory);
            makeInventoryDroppable($inventory);
            initializePaperdollSlots();
            makeItemsDraggable();
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
                    text: $('<div></div>').html(renderInfoText($item))
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
        var slotsString = $item.data('slots');

        var infoText = '';

        if (description != '') {
            infoText += description + '<br>';
        }

        if (slotsString != '') {
            var slots = slotsString.split(',');

            infoText += '<br><strong>Можно надеть:</strong> ';

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
        }

        return infoText;
    }

    /**
     * Настройка draggable-предметов
     */
    function makeItemsDraggable() {
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
        $inventory.find('.paperdoll .slot').each(function (key, element) {
            var $slot = $(element);
            var slotName = $slot.data('slot');

            //TODO[Rottenwood]: Хак для колец. Нужно исправить: предметы должны
            //TODO[Rottenwood]: одеваться во все доступные для них слоты
            if (slotName == 'ring_first' || (slotName == 'ring_second')) {
                slotName += ', .slot.ring';
            }

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
                        var availableSlots = $slot.data('slots').split(',');

                        // Подсветка подходящих для предмета слотов
                        availableSlots.forEach(function (availableSlot) {
                            $inventory.find('.paperdoll .slot.' + availableSlot).addClass('highlight');
                        });
                        $draggingItem.removeClass('highlight');

                        $slot.data('previous-image', $slotImage.attr('src'));
                        $slotImage.attr('src', $slot.data('img'));
                        $draggingItem.css('background-color', 'transparent');
                        $draggingItem.css('border', 'none');
                    },
                    stop: function () {
                        $inventory.find('.paperdoll .slot').removeClass('highlight');
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
            }
        });
    }

    /**
     * Надеть предмет
     * @param $item
     * @param $slot
     */
    function wearItem($item, $slot) {
        var slotName = $slot.data('slot');
        var itemQtip = $item.qtip('api');
        var itemId = $item.data('id');

        $slot.addClass('dressed');
        $slot.removeClass('nointeract');
        itemQtip.destroy();

        $slot.qtip({
            content: {
                title: itemQtip.get('content.title'),
                text: $('<div></div>').html(renderInfoText($item))
            },
            position: itemQtip.get('position'),
            style: itemQtip.get('style')
        });

        $slot.data('id', itemId);
        $slot.data('name', $item.data('name'));
        $slot.data('name4', $item.data('name4'));
        $slot.data('description', $item.data('description'));
        $slot.data('slots', $item.data('slots'));

        $slot.find('img').attr('src', $item.find('img').attr('src'));

        // Если предмет одет из инвентаря или из другого слота
        if ($item.hasClass('item')) {
            $item.remove();

            Kingdom.Chat.addInfo('Ты надел ' + $slot.data('name4'));
        } else if ($item.hasClass('slot')) {
            $item.find('img').attr('src', $item.data('img'));

            Kingdom.Chat.addInfo('Ты переодел ' + $slot.data('name4'));
        }

        $inventory.find('.paperdoll .slot').removeClass('highlight');

        initializePaperdollSlots();

        Kingdom.Websocket.command('wear', [itemId, slotName]);

        ion.sound.play('wear-clothes');
    }

    /**
     * Снять предмет
     * @param $slot
     */
    function removeItem($slot) {
        var slotName = $slot.data('slot');
        var slotQtip = $slot.qtip('api');

        $slot.addClass('nointeract');
        $slot.removeClass('dressed');
        $slot.draggable('destroy');
        slotQtip.destroy();

        var $item = $('<div class="item ' + $slot.data('slots').split(',').join(' ') + '"></div>');

        $item.data('id', $slot.data('id'));
        $item.data('name', $slot.data('name'));
        $item.data('name4', $slot.data('name4'));
        $item.data('description', $slot.data('description'));
        $item.data('slots', $slot.data('slots'));

        $item.html('<img src="' + $slot.data('previous-image') + '">');

        $item.qtip({
            content: {
                title: slotQtip.get('content.title'),
                text: $('<div></div>').html(renderInfoText($item))
            },
            position: slotQtip.get('position'),
            style: slotQtip.get('style')
        });

        $inventory.find('.items-list').append($item);
        $inventory.find('.paperdoll .slot').removeClass('highlight');

        makeItemsDraggable();

        Kingdom.Chat.addInfo('Ты снял ' + $item.data('name4'));

        Kingdom.Websocket.command('remove', slotName);

        ion.sound.play('remove-clothes');
    }
});
