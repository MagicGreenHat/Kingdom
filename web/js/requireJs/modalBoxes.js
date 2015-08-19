/**
 * Модальные блоки интерфейса
 */
define(['jquery', 'command', 'websocketSession', 'money'], function ($, callCommand, sessionData, money) {
    var session = sessionData.session;
    var localChannelName = sessionData.localChannelName;

    $(function () {
        var $room = $('#game-room');
        var $inventory = $('#game-inventory');
        var $userInfo = $('#game-user-info');
        var userName = $userInfo.data('name');


        /**
         * Подключение класса Инвентаря и его инициация
         */
        requirejs(['inventory'],function(inventory) {
            inventory.init();
        });

        money.init();

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

            callCommand('lookUser', userName);

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


        /**
         * Подписка на сообщения от сервера
         */
        session.subscribe(localChannelName, function (args) {
            var data = JSON.parse(args[0]);

            if (data.commandName == 'lookUser') {
                var avatar = data.data.avatar;

                $userInfo.find('.avatar').html('<img src="' + avatar + '">');
            }
        });
    });
});
