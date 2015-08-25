$(function () {
    var connection = new autobahn.Connection({
        url: 'ws://' + window.location.hostname + ':7777', // параметр передается из twig-шаблона
        realm: 'kingdom'
    });

    connection.onopen = function (session) {
        var userData = {
            sessionId: sessionId // параметр передаeтся из twig-шаблона
        };

        // Запись вебсокет-сессии
        Kingdom.Websocket.register(session, sessionId);

        /**
         * Вызов удаленной процедуры
         */
        session.call('gate', [userData]).then(
            function () {
                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

                    //TODO[Rottenwood]: Убрать блок обработки в новый файл (напр. commandHandler.js)
                    // Обработка результатов запрошенных команд
                    if (data.commandName == 'move') {
                        if (!data.errors) {
                            Kingdom.Websocket.command('composeMap');
                        }
                    } else if (data.commandName == 'composeMap') {
                        redrawRoom(data.data);
                        Kingdom.Websocket.command('showPlayersInRoom');
                    } else if (data.commandName == 'showPlayersInRoom') {
                        showPlayersInRoom(data.data);
                    } else if (data.commandName == 'moveAnother') {
                        addInfo(data.message);
                        Kingdom.Websocket.command('showPlayersInRoom');
                    } else if (data.commandName == 'reloadPage') {
                        location.reload();
                    } else if (data.commandName == 'inventory') {
                        //TODO[Rottenwood]: Сделать модель Kingdom.Inventory. Сделать контроллер
                        Inventory.setInventory(data.data);
                    } else if (data.commandName == 'getMoney') {
                        Kingdom.Money.setMoney(data.data);
                    } else if (data.commandName == 'lookUser') {
                        Kingdom.User.renderAvatar(data.data.avatar);
                    }

                    // Отрисовка карты
                    if (data.mapData) {
                        redrawMap(data.mapData);
                    }

                    // Отрисовка чата
                    if (data.chat) {
                        addChatPhrase(data.chat);
                    }

                    // Вывод информационного сообщения
                    if (data.info) {
                        addInfo(data.info);
                    }

                    // Вывод количества игроков онлайн
                    if (data.playersOnlineCount) {
                        showOnline(data.playersOnlineCount);
                    }
                });

                // Кнопки перемещения
                $('.map .direction').on('click', function () {
                    var direction = $(this).data('direction');
                    Kingdom.Websocket.command('move', direction);
                });

                // Поле для чата
                $('#chat-input').keypress(function (event) {
                    if (event.which == 13) {
                        Kingdom.Websocket.command('chat', $(this).val());
                        $(this).val('');
                        return false;
                    }
                });

                /////// Вызов команд при загрузке страницы ///////
                Kingdom.Websocket.command('composeMap');
                Kingdom.Websocket.command('who');
                Kingdom.Websocket.command('inventory');
                Kingdom.Websocket.command('getMoney');
            }
        );
    };

    var pingInterval;
    connection.onclose = function () {
        clearInterval(pingInterval);
        ping(window.location.origin);
        pingInterval = setInterval(function () {
            ping(window.location.origin);
        }, 3000);

        var $gameOverlay = $('#game-overlay');
        var $systemMessage = $('#system-message');

        function ping(url){
            $.ajax({
                url: url,
                success: function(){
                    window.location.reload();
                },
                error: function(){
                    $gameOverlay.show();
                    $systemMessage.show();
                }
            });
        }
    };


    connection.open();

    /////// Функции клиентского интерфейса ///////

    var $gameContentRoom = $('#game-room');
    var $gameMap = $('#game-map');
    var $gameChat = $('#game-chat');

    /**
     * Отрисовка карты
     * @param mapData
     */
    function redrawMap(mapData) {
        $('#game-map .map-frame img').attr('src', '/img/locations/null.png');

        mapData.forEach(function (room) {
            $('.map .y' + room.y + ' .x' + room.x)
                .html('<img src="/img/locations/' + room.pic + '.png">');
        });
    }

    /**
     * Добавление сообщения в чат
     * @param chatData
     */
    function addChatPhrase(chatData) {
        $gameChat.append('<div><strong>' + chatData.from + '</strong>: ' + chatData.phrase.replace(/<[^>]+>/gi, '') + '</div>');
        $gameChat.scrollTop($gameChat.prop("scrollHeight"));
    }

    /**
     * Добавление информационной строки в чат
     * @param infoData
     */
    function addInfo(infoData) {
        var html;
        if (infoData.event == 'playerEnter') {
            html = '<div><strong>' + infoData.name + ' вошел в игру.</strong></div>';
        } else if (infoData.event == 'advice') {
            html = '<div><strong>Игровая информация:</strong> ' + infoData.advice + '</div>';
        } else {
            html = '<div>' + infoData + '.</div>';
        }

        $gameChat.append(html);
        $gameChat.scrollTop($gameChat.prop('scrollHeight'));
    }

    /**
     * Отображение игроков онлайн
     * @param playersOnlineCount
     */
    function showOnline(playersOnlineCount) {
        $('#game-chat .hello-username .players-online').html('Игроков онлайн: ' + playersOnlineCount);
    }

    /**
     * Перерисовка комнаты
     * @param roomData
     */
    function redrawRoom(roomData) {
        var $roomName = $gameContentRoom.find('.room-name');
        var $roomDescription = $gameContentRoom.find('.room-description');
        var $roomPlayers = $gameContentRoom.find('.room-players');

        $roomPlayers.html('');
        $roomName.html('').html(roomData.name + '<span class="coordinates">[' + roomData.x + '/' + roomData.y + ']</span>');
        $roomDescription.html('').html(roomData.description);
    }

    /**
     * Отрисовка игроков в комнате
     * @param players
     */
    function showPlayersInRoom(players) {
        var $roomPlayers = $gameContentRoom.find('.room-players');

        $roomPlayers.html('');

        players.forEach(function (player) {
            $roomPlayers.append('<div class="user-info" data-name="' + player.name + '">' + player.name + ' ' + player.stance + '</div><br>');
        });
    }

    /////// События ///////
    var $directionMapFrame = $('.map .map-frame.direction');

    $gameMap.hover(function () {
        var $directionMapFrame = $('.map .map-frame.direction');
        $directionMapFrame.toggleClass('arrow');
    });

    $gameMap.on('mousemove', function () {
        if (!$directionMapFrame.hasClass('arrow')) {
            $directionMapFrame.addClass('arrow');
        }
    });
});
