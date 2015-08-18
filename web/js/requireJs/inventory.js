define(['jquery'], function ($) {


var Inventory = (function() {

    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');

    var allHide = function() {
        $room.hide();
        $userInfo.hide();
    }

    var openInventory = function() {
        if (typeof (window.inventory) != 'undefined' && window.inventory!='') {
            allHide();
            $inventory.show();
        }
        else {
            console.log('Инвентарь еще не загружен!');
        }
    }

        /**
         * Отображение всего, что касается инвентаря
         * @type {{allItems: allItems}}
         */
        var views = {

            allItems: function() {

                console.log(window.inventory);

                window.inventory.forEach(function(item) {
                    if (item.quantity==1) {
                        $(".paperdoll ."+item.allowedSlots[0]+" img").prop("src","/img/items/"+item.pic+"_color.png");
                    }
                    else if (item.quantity>1) {
                        $(".paperdoll ."+item.allowedSlots[0]+" img").prop("src","/img/items/"+item.pic+"_color.png");
                        $(".paperdoll ."+item.allowedSlots[0]).append('<div class="count_item">'+item.quantity+'</div>');
                    }
                    else {
                        $(".paperdoll ."+item.allowedSlots[0]+" img").prop("src","/img/items/"+item.pic+".png");
                    }
                });
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

return Inventory;
});