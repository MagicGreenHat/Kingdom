//TODO[Rottenwood]: Рефакторинг в модели и контроллеры

/**
 * Модальные блоки интерфейса
 */
$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');
    var userName = $userInfo.data('name');
    var userAvatar = $userInfo.data('avatar');

    $('.open-user-info-button').click(function () {
        openUserInfo(userName);
    });

    $userInfo.find('.close-button').click(function () {
        openRoomBox();
    });

    $room.find('.room-players').on('click', '.user-info', function () {
        openUserInfo($(this).data('name'));
    });

    /**
     * Открытие окна информации об игроке
     */
    function openUserInfo(userNameToLook) {
        $userInfo.find('.user-name').html(userNameToLook);
        $userInfo.find('.avatar').html('');

        if (userName == userNameToLook) {
            $('#game-user-info').find('.avatar').html('<img src="' + userAvatar + '">');
        } else {
            $('#game-user-info').find('.avatar').html('');
            Kingdom.Websocket.command('lookUser', userNameToLook);
        }

        $userInfo.show();
        $room.hide();
        $inventory.hide();
    }

    /**
     * Открытие окна комнаты
     */
    function openRoomBox() {
        $room.show();
        $inventory.hide();
        $userInfo.hide();
    }
});
