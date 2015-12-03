/**
 * Игровая комната
 */
Kingdom.Room = (function () {
    var name;

    return {

        /**
         * Установка имени комнаты
         * @param roomName
         */
        setName: function (roomName) {
            name = roomName;
        },

        /**
         * Запрос имени комнаты
         */
        getName: function () {
            return name;
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
