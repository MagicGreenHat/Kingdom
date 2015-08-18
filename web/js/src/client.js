define(['jquery', 'autobahn'], function ($, autobahn) {
    $(function () {
        var connection = new autobahn.Connection({
            url: 'ws://' + websocketUrl + ':7777', // параметр передается из twig-шаблона
            realm: 'kingdom'
        });

        connection.onopen = function (session) {
            var userData = {
                sessionId: sessionId // параметр передаeтся из twig-шаблона
            };

            /**
             * Регистрация удаленной процедуры для отслеживания дисконнекта
             */
            session.register('online.' + sessionId, function () {});

            /**
             * Вызов удаленной процедуры
             */
            session.call('gate', [userData]).then(
                function () {
                    var localChannelName = 'character.' + sessionId;

                    /**
                     * Модуль вебсокет-сессии
                     */
                    define('websocketSession', function () {
                        return {
                            session: session,
                            localChannelName: localChannelName
                        };
                    });

                    /**
                     * Модуль отправки команды по локальному каналу
                     * @param command
                     * @param arguments строка, или несколько аргументов разделенных символом :
                     */
                    define('command', function () {
                        return function (command, arguments) {
                            callCommand(command, arguments);
                        };
                    });

                    /**
                     * Подключение модуля модальных окон
                     */
                    requirejs(['modalBoxes']);

                    session.subscribe(localChannelName, function (args) {
                        console.log(args[0]);

                        var data = JSON.parse(args[0]);

                        //TODO[Rottenwood]: Убрать блок обработки в новый файл (напр. commandHandler.js)
                        // Обработка результатов запрошенных команд
                        if (data.commandName == 'move') {
                            if (!data.errors) {
                                callCommand('composeMap');
                            }
                        } else if (data.commandName == 'composeMap') {
                            redrawRoom(data.data);
                            callCommand('showPlayersInRoom');
                        } else if (data.commandName == 'showPlayersInRoom') {
                            showPlayersInRoom(data.data);
                        } else if (data.commandName == 'moveAnother') {
                            addInfo(data.message);
                            callCommand('showPlayersInRoom');
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

                    /**
                     * Отправка команды по локальному каналу
                     * @param command
                     * @param arguments строка, или несколько аргументов разделенных символом :
                     */
                    function callCommand(command, arguments) {
                        //TODO[Rottenwood]: блокировка интерфейса отправки команд

                        //TODO[Rottenwood]: if (typeof arguments == 'object') { // implode }

                        session.publish(localChannelName, [{command: command, arguments: arguments}]);
                        //TODO[Rottenwood]: разблокировка интерфейса отправки команд
                    }

                    // Кнопки перемещения
                    $('.map .direction').on('click', function () {
                        var direction = $(this).data('direction');
                        callCommand('move', direction);
                    });

                    // Поле для чата
                    $('#chat-input').keypress(function (event) {
                        if (event.which == 13) {
                            callCommand('chat', $(this).val());
                            $(this).val('');
                            return false;
                        }
                    });

                    /////// Вызов команд при загрузке страницы ///////
                    callCommand('composeMap');
                    callCommand('who');
                }
            );
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

            players.forEach(function(player) {
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
});
