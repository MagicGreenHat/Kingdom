/**
 * Приложение для отправки сообщения всем игрокам
 */

var config = require('./config/config.json');
var autobahn = require('autobahn');
var redis = require('then-redis').createClient();

var broadcastMessage = process.argv.slice(2).join(" ");

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    sendToAllOnlinePlayers(broadcastMessage);

    function sendToAllOnlinePlayers(message) {
        redis.hgetall(config.redisIdSessionHash).then(function (sessions) {
            console.log('[Оповещение]: ' + message);
            for (var property in sessions) {
                if (sessions.hasOwnProperty(property)) {
                    var channel = 'character.' + sessions[property];
                    var messageJson = JSON.stringify({info:{event: "broadcast", message: message}});

                    session.publish(channel, [messageJson]);
                }
            }

            process.exit();
        });
    }
};

connection.open();
