/**
 * Вебсокет роутер реализующий протокол WAMP
 */

//WAMPRT_TRACE = true; // Вывод технической информации
WEBSOCKET_PORT = 7777;

LOG_CHANNEL = 'logger';

REDIS_ID_USERNAME_HASH = 'kingdom:users:usernames';
REDIS_ID_SESSION_HASH = 'kingdom:users:sessions';
REDIS_SESSION_ID_HASH = 'kingdom:sessions:users';
REDIS_ONLINE_LIST = 'kingdom:users:online';

var Router = require('wamp.rt');
var redis = require('then-redis').createClient();

redis.on('error', function (err) {
   console.log('[!] Redis error ' + err);
});

var app = new Router({port: WEBSOCKET_PORT});

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
        redis.hget(REDIS_SESSION_ID_HASH, clientData[1]).then(function (userId) {
            redis.sadd(REDIS_ONLINE_LIST, userId);

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
       redis.hget(REDIS_SESSION_ID_HASH, clientData[1]).then(function(userId) {
           redis.srem(REDIS_ONLINE_LIST, userId);

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
    redis.hget(REDIS_ID_USERNAME_HASH, userId).then(function (userName) {
        if (userName) {
            app.publish(LOG_CHANNEL, 1, [JSON.stringify({
                event: eventType,
                userId: userId,
                userName: userName
            })]);
        }
    });
}

console.log('WebSocket router is running ...');
