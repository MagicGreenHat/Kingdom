$(function () {
    var $gameRoom = $('#game-room');
    var $gameInventory = $('#game-inventory');

    /**
     * Открытие инвентаря
     */
    function openInventory() {
        $gameRoom.hide();
        $gameInventory.show();
    }

    /**
     * Закрытие инвентаря
     */
    function closeInventory() {
        $gameRoom.show();
        $gameInventory.hide();
    }

    $('.open-inventory-button').click(function() {
        openInventory();
    });

    $('.game-inventory-close-button').click(function() {
        closeInventory();
    });
});
