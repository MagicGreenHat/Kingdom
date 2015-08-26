/**
 * Вебсокет роутер реализующий протокол WAMP
 */

//WAMPRT_TRACE = true; // Вывод технической информации

var config = require('./config/config.json');
var Router = require('wamp.rt');
var redis = require('then-redis').createClient();

redis.on('error', function (err) {
   console.log('[!] Redis error ' + err);
});

var app = new Router({port: config.websocketPort});

// Событие при публикации
app.on('Publish', function (topicUri, args) {
    console.log('[' + topicUri + ']:', args[0]);
});

// Событие при подключении удаленной процедуры
app.on('RPCRegistered', function (topicUri) {
    var channel = topicUri[0];

    if (channel.lastIndexOf('online.', 0) === 0) {
        var clientData = channel.split(".");

        // Добавление id игрока в redis-список онлайн игроков
        redis.hget(config.redisSessionIdHash, clientData[1]).then(function (userId) {
            redis.sadd(config.redisOnlineList, userId);

            logEvent('userEnter', userId);
        });
    }
});

// Событие при отключении удаленной процедуры
app.on('RPCUnregistered', function (topicUri) {
   var channel = topicUri[0];

   if (channel.lastIndexOf('online.', 0) === 0) {
       var clientData = channel.split(".");

       // Удаление id игрока из redis-списка онлайн игроков
       redis.hget(config.redisSessionIdHash, clientData[1]).then(function(userId) {
           redis.srem(config.redisOnlineList, userId);

           logEvent('userExit', userId);
       });
   }
});

/**
 * Отправка в канал логирования события
 * @param eventType string
 * @param userId int
 */
function logEvent(eventType, userId) {
    redis.hget(config.redisIdUsernameHash, userId).then(function (userName) {
        if (userName) {
            app.publish(config.logChannel, 1, [JSON.stringify({
                event: eventType,
                userId: userId,
                userName: userName
            })]);
        }
    });
}

console.log('WebSocket router is running ...');
