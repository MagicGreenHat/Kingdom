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
         * Плюрализатор
         *
         * @Usage
         * pluralize(1, 'секунду', 'секунды', 'секунд'); // returns "секунду"
         * pluralize(10, 'секунду', 'секунды', 'секунд'); // returns "секунд"
         *
         * @param number int
         * @param one string
         * @param two string
         * @param five string
         * @returns string
         */
        pluralize: function (number, one, two, five) {
            number = Math.abs(number);
            number %= 100;
            if (number >= 5 && number <= 20) {
                return five;
            }
            number %= 10;
            if (number == 1) {
                return one;
            }
            if (number >= 2 && number <= 4) {
                return two;
            }
            return five;
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

            $gameChat.find('.warning').delay(3000).fadeOut(1000);
        }
    }
})();
