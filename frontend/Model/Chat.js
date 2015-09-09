Kingdom.Chat = (function () {

    /**
     * Удаление персонажа из комнаты
     * @param playerName
     */
    function removePlayerFromRoom(playerName) {
        $('#game-room div.room-players div').each(function () {
            var $this = $(this);

            if($this.data('name') == playerName) {
                $this.remove();
            }
        });
    }

    return {

        /**
         * Отрисовка сообщения в чате
         * @param chatData
         */
        addChatPhrase: function (chatData) {
            var $gameChat = $('#game-chat');

            $gameChat.append('<div><strong>' + chatData.from + '</strong>: ' + chatData.phrase.replace(/<[^>]+>/gi, '') + '</div>');
            $gameChat.scrollTop($gameChat.prop("scrollHeight"));
        },

        /**
         * Добавление информационной строки в чат
         * @param infoData
         */
        addInfo: function (infoData) {
            var $gameChat = $('#game-chat');

            var html;
            if (infoData.event == 'playerEnter') {
                html = '<div><strong>' + infoData.name + ' вошел в игру.</strong></div>';
                Kingdom.Websocket.command('showPlayersInRoom');
            } else if (infoData.event == 'playerExit') {
                html = '<div><strong>' + infoData.name + ' вышел из игры.</strong></div>';
                removePlayerFromRoom(infoData.name);
            } else if (infoData.event == 'advice') {
                html = '<div><strong>Игровая информация:</strong> ' + infoData.advice + '</div>';
            } else if (infoData.event == 'broadcast') {
                html = '<div class="broadcast-message"><strong>Внимание:</strong> ' + infoData.message + '</div>';
            } else if (infoData.event == 'warning') {
                html = '<div class="warning">' + infoData.message + '.</div>';
            } else if (infoData.event == 'obtainWood') {
                html = '<div>' + infoData.name + ' рубит дерево.</div>';
                Kingdom.Room.updateResources(infoData.resources);
                if (infoData.typeChanged) {
                    Kingdom.Websocket.command('composeMap');
                }
            } else {
                html = '<div>' + infoData + '.</div>';
            }

            $gameChat.append(html);
            $gameChat.scrollTop($gameChat.prop('scrollHeight'));
        }
    }
})();
