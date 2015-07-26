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

    session.subscribe('system', function (args) {
        var data = args[0];

        console.log('Event:', data);
    });

    session.publish('system', [userData]);

    session.subscribe('character.' + session.id, function (data) {
        console.log(data);
    });

    session.call('system.register.hash', [userData.hash]).then(
        function (result) {
            console.log('Result:', result);
        }
    );
};

connection.open();

