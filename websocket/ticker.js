/**
 * Сервер для отсчета времени
 */

SECONDS_IN_TICK = 60;
REDIS_ID_SESSION_HASH = 'kingdom:users:sessions';

var autobahn = require('autobahn');
var redis = require('then-redis').createClient();

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    var sendToAllOnlinePlayers = function (message) {
        redis.hgetall(REDIS_ID_SESSION_HASH).then(function (sessions) {
            for (var property in sessions) {
                if (sessions.hasOwnProperty(property)) {
                    var channel = 'character.' + sessions[property];
                    var messageJson = JSON.stringify(message);

                    session.publish(channel, [messageJson]);
                }
            }
        });
    };

    var tick = 1;

    function processTick() {
        sendToAllOnlinePlayers('tick #' + tick);
        tick++;
    }

    processTick();
    setInterval(processTick, SECONDS_IN_TICK * 1000);

};

connection.open();
