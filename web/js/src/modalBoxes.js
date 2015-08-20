//TODO[Rottenwood]: Рефакторинг в модели и контроллеры

/**
 * Модальные блоки интерфейса
 */
$(function () {
    var $room = $('#game-room');
    var $inventory = $('#game-inventory');
    var $userInfo = $('#game-user-info');
    var userName = $userInfo.data('name');

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
    function openUserInfo(userName) {
        $userInfo.find('.user-name').html(userName);
        $userInfo.find('.avatar').html('');

        Kingdom.Websocket.command('lookUser', userName);

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
