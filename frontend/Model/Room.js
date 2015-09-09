Kingdom.Room = (function () {
    return {
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
