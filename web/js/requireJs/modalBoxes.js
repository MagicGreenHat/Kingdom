/**
 * Модальные блоки интерфейса
 */
define(['jquery', 'command'], function ($, callCommand) {
    $(function () {
        var $room = $('#game-room');
        var $inventory = $('#game-inventory');
        var $userInfo = $('#game-user-info');
        var userName = $userInfo.data('name');

        $('.open-inventory-button').click(function () {
            openInventory();
        });

        $('.open-user-info-button').click(function () {
            openUserInfo(userName);
        });

        $inventory.find('.close-button').click(function () {
            openRoomBox();
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
         * Открытие окна инвентаря
         */
        function openInventory() {
            $inventory.show();
            $userInfo.hide();
            $room.hide();
        }
    });
});
