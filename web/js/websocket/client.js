var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

$(function () {
    connection.onopen = function (session) {
        var userData = {
            sessionId: sessionId // параметры передаются из twig-шаблона
        };

        // Регистрация удаленной процедуры для отслеживания дисконнекта
        session.register('online.' + sessionId, function () {});

        session.call('gate', [userData]).then(
            function () {
                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

                    // Обработка результатов запрошенных команд
                    if (data.command == 'move') {
                        if (!data.errors) {
                            callCommand('composeMap');
                        }
                    } else if (data.command == 'composeMap') {
                        redrawRoom(data.data);
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

                // Отправка команды по локальному каналу
                function callCommand(command, arguments) {
                    //TODO[Rottenwood]: блокировка интерфейса отправки команд
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

    var $gameContent = $('#game-content');
    var $gameMap = $('#game-map');
    var $gameChat = $('#game-chat');

    function redrawMap(mapData) {
        $('#game-map .map-frame img').attr('src', '../../img/locations/null.png');

        mapData.forEach(function (room, i) {
            $('.map .y' + room.y + ' .x' + room.x)
                .html('<img src="../../img/locations/' + room.pic + '.png">');
        });
    }

    function addChatPhrase(chatData) {
        $gameChat.append('<div><strong>' + chatData.from + '</strong>: ' + chatData.phrase.replace(/<[^>]+>/gi, '') + '</div>');
        $gameChat.scrollTop($gameChat.prop("scrollHeight"));
    }

    function addInfo(infoData) {
        if (infoData.event = 'playerEnter') {
            var html = '<div><strong>' + infoData.name + ' вошел в игру.</strong></div>';
        }

        $gameChat.append(html);
        $gameChat.scrollTop($gameChat.prop("scrollHeight"));
    }

    function showOnline(playersOnlineCount) {
        $('#game-chat .hello-username').append('Игроков онлайн: ' + playersOnlineCount);
    }

    function redrawRoom(roomData) {
        var $roomName = $gameContent.find('.room-name');
        var $roomDescription = $gameContent.find('.room-description');

        $roomName.html('').html(roomData.name + '<span class="coordinates">[' + roomData.x + '/' + roomData.y + ']</span>');
        $roomDescription.html('').html(roomData.description);
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
