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
                // TODO[Rottenwood]: Отрисовка игрового интерфейса

                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

                    //TODO[Rottenwood]: Обработка перехода
                    if (data.picture) {
                        callCommand('composeMap');
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
            }
        );
    };

    connection.open();

    /////// Функции клиентского интерфейса ///////

    var $gameChat = $('#game-chat');
    var $gameMap = $('#game-map');

    function redrawMap(mapData) {
        $gameMap.find('.map-frame').html('<img src="../../img/locations/null.png">');

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

    /////// Вызов функций при загрузке страницы ///////
    var $directionMapFrame = $('.map .map-frame.direction');

    $gameMap.hover(function () {
        var $directionMapFrame = $('.map .map-frame.direction');
        $directionMapFrame.toggleClass('arrow');
    });

    $gameMap.on('mousemove', function () {
        if(!$directionMapFrame.hasClass('arrow')){
            $directionMapFrame.addClass('arrow');
        }
    });
});
