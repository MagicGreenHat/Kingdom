USER_HASH = 'hash';

var autobahn = require('autobahn');

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

connection.onopen = function (session) {
    var userData = {
        session: session.id,
        hash: USER_HASH
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

            callCammand('north');
            //callCammand('south');
        }
    );
};

connection.open();


function redrawMap(mapData) {
    console.log('Карта отрисована: ' + mapData.a1 + mapData.a2 + mapData.a3);
}
