//WAMPRT_TRACE = true; // Verbose output
WEBSOCKET_PORT = 7777;
REDIS_CHARACTERS_HASH = 'kingdom:characters:hash';
REDIS_CHARACTERS_HASH_TEMPORARY = 'kingdom:characters:hash:temp';

var Router = require('wamp.rt');
var redis = require('redis').createClient();

redis.on('error', function (err) {
   console.log('[!] Redis error ' + err);
});

// WebSocket router
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
        var sessionId = clientData[1];

        //Запрос userId и username по id сессии из redis
        redis.hget(REDIS_CHARACTERS_HASH_TEMPORARY, sessionId, function(err, characterDataJson) {
            // Добавление пользователя в хэш онлайн игроков в redis
            redis.hset(REDIS_CHARACTERS_HASH, sessionId, characterDataJson);
        });
    }
});

// Событие при отключении удаленной процедуры
app.on('RPCUnregistered', function (topicUri) {
   var channel = topicUri[0];

   if (channel.lastIndexOf('online.', 0) === 0) {
       var clientData = channel.split(".");

       // Удаление пользователя из хэша онлайн игроков в redis
       redis.hdel(REDIS_CHARACTERS_HASH, clientData[1]);
   }
});

console.log('WebSocket router is running ...');
