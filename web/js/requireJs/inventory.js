define(['jquery'], function ($) {

var Inventory = function() {

    var $inventory = $('#game-inventory');

    var openInventory = function() {
        callCommand('inventory');

        $inventory.show();

        allAnother.close() // тут какая-то другая функция - общая для закрытия лишних окон

    }

    /**
     * Отображение всего, что касается инвентаря
     * @type {{allItems: allItems}}
     */
    var views = {

        allItems: function(data) {
            var html = '';
            data.forEach(function(i) {
                html += '<div class="inventoryItem"><img src="/img/items/'+ i.pic+'.png"></div>';
            });
            $inventory.children('.container').html(html);
        }

    }

    return {

        init: function() {
            $('.open-inventory-button').click(function () {
                openInventory();
            });

            $inventory.children('.close-button').click(function () { // кажется children работает быстрее fined, но не увере, надо почитать
                openRoomBox(); // тут опять же стандартная функци для закрытия всего и открытия roomBox
            });
        },

        printItems: views.allItems
    }
}

return new Inventory();
});