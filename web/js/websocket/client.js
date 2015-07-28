var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

$(function () {
    connection.onopen = function (session) {
        var userData = {
            session: session.id,
            hash: hash
        };

        session.call('gate', [userData]).then(
            function (result) {
                console.log('Result:', result);

                // TODO: Отрисовка игрового интерфейса

                var localChannelName = 'character.' + session.id;

                session.subscribe(localChannelName, function (args) {
                    var data = args[0];

                    console.log(data);

                    // Команды для выполнения на клиенте
                    // Отрисовка карты
                    if (data.map) {
                        redrawMap(data.map);
                    }
                });

                function callCammand(command) {
                    //TODO: блокировка интерфейса отправки команд
                    session.publish(localChannelName, [{command: command}]);
                    //TODO: разблокировка интерфейса отправки команд
                }

                // Команды перемещения
                $('button').on('click', function() {
                    var direction = $(this).attr('class');
                    callCammand(direction);
                });
            }
        );
    };

    connection.open();

    //TODO: Отрисовка элементов карты
    function redrawMap(mapData) {
        if (mapData.a1 == 1) {
            $('.map-frame.map1').css('background-color', 'yellow');
        } else {
            $('.map-frame.map1').css('background-color', 'red');
        }

        console.log('Карта отрисована: ' + mapData.a1 + mapData.a2 + mapData.a3);
    }
});
