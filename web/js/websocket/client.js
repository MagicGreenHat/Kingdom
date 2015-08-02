var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

$(function () {
    connection.onopen = function (session) {
        var userData = {
            sessionId: sessionId
        };

        session.call('gate', [userData]).then(
            function (result) {
                console.log('Result:', result);

                // TODO[Rottenwood]: Отрисовка игрового интерфейса

                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

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
                $('button').on('click', function() {
                    var direction = $(this).attr('class');
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

    //TODO[Rottenwood]: Отрисовка элементов карты
    function redrawMap(mapData) {
        if (mapData.a1 == 1) {
            $('.map-frame.map1').css('background-color', 'yellow');
        } else {
            $('.map-frame.map1').css('background-color', 'red');
        }

        console.log('Карта отрисована: ' + mapData.a1 + mapData.a2 + mapData.a3);
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
});
