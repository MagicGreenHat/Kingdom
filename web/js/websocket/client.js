var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

$(function () {
    connection.onopen = function (session) {
        var userData = {
            sessionId: sessionId // sessionId передается из html-шаблона
        };

        session.call('gate', [userData]).then(
            function () {
                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

                    // Обработка результатов запрошенных команд
                    if (data.command == 'move') {
                        //TODO[Rottenwood]: Добавить в composeMap вывод данных о центральной комнате
                        callCommand('composeMap');

                        if (data.data) {
                            redrawRoom(data.data);
                        }
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
        $gameChat.append('<div><strong>' + chatData.from + '</strong>: ' + chatData.phrase + '</div>');
        $gameChat.scrollTop($gameChat.prop("scrollHeight"));
    }

    function addInfo(infoData) {
        if (infoData.event = 'playerEnter') {
            var html = '<div><strong>' + infoData.name + ' вошел в игру.</strong></div>';
        }

        $gameChat.append(html);
        $gameChat.scrollTop($gameChat.prop("scrollHeight"));
    }

    function redrawRoom(roomData) {
        $gameContent.find('.room-name').html(roomData.name);
        $gameContent.find('.room-description').html(roomData.description);
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
