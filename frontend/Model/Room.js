/**
 * Текущая игровая комната
 */
Kingdom.Room = (function () {
    var name,
        x,
        y;

    return {

        /**
         * Установка параметров комнаты
         * @param roomData
         */
        setData: function (roomData) {
            name = roomData.name;
            x = roomData.x;
            y = roomData.y;
        },

        /**
         * Запрос имени комнаты
         * @returns string
         */
        getName: function () {
            return name + ' [' + x + '/' + y + ']';
        },

        /**
         * @param resourcesData
         */
        updateResources: function (resourcesData) {
            var $gameContentRoom = $('#game-room');

            $.each(resourcesData, function (resourceId, resourceQuantity) {
                var $resourceQuantity = $gameContentRoom.find('.room-resources-list .resource.' + resourceId + ' .quantity');
                $resourceQuantity.html(resourceQuantity);
            });

            Kingdom.Websocket.command('inventory');
            ion.sound.play('obtain-tree');
        }
    }
})();
