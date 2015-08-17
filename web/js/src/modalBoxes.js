$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');

    $('.open-inventory-button').click(function() {
        $room.hide();
        $userInfo.hide();
        $inventory.show();
    });

    $inventory.find('.close-button').click(function() {
        $room.show();
        $inventory.hide();
    });

    $('.open-user-info-button').click(function() {
        $room.hide();
        $inventory.hide();
        $userInfo.show();
    });

    $userInfo.find('.close-button').click(function() {
        $room.show();
        $userInfo.hide();
    });
});
