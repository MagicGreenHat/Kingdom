var Inventory = (function() {

    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');

    var allHide = function() {
        $room.hide();
        $userInfo.hide();
    };

    var openInventory = function() {
        allHide();
        $inventory.show();
    };

        /**
         * Отображение всего, что касается инвентаря
         * @type {{allItems: allItems}}
         */
        var views = {

            allItems: function() {

                var html = '';
                window.inventory.forEach(function (item) {
                    html += '<div class="item"><img src="/img/items/' + item.pic + '.png"></div>';
                });

                $inventory.children('.items-list').html(html);
            }

        };

        return {
            init: function () {
                $('.open-inventory-button').click(function () {
                    openInventory();
                });


                $inventory.find('.close-button').click(function () { // кажется children работает быстрее fined, но не увере, надо почитать
                    $room.show();
                    $userInfo.hide();
                    $inventory.hide();
                });
            },

            setInventory: function(obj) {
                window.inventory = obj;
                views.allItems();
            }

        }
})();

Inventory.init();
