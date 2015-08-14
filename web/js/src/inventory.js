$(function () {
    var $gameRoom = $('#game-room');
    var $gameInventory = $('#game-inventory');

    $('.open-inventory-button').click(function() {
        $gameRoom.hide();
        $gameInventory.show();
    });

    $('.game-inventory-close-button').click(function() {
        $gameRoom.show();
        $gameInventory.hide();
    });
});
