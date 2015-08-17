$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');

    $('.open-inventory-button').click(function() {
        $room.hide();
        $userInfo.hide();
        $inventory.show();
    });

    $('#game-inventory .close-button').click(function() {
        $room.show();
        $inventory.hide();
    });

    $('.open-user-info-button').click(function() {
        $room.hide();
        $inventory.hide();
        $userInfo.show();
    });

    $('#game-user-info .close-button').click(function() {
        $room.show();
        $userInfo.hide();
    });
});
