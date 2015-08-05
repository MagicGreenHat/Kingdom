//WAMPRT_TRACE = true; // Verbose output
WEBSOCKET_PORT = 7777;
REDIS_CHARACTERS_HASH = 'kingdom:characters:hash';

var Router = require('wamp.rt');
var redis = require("redis").createClient();

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
        var userDataJson = JSON.stringify({id: clientData[2], name: clientData[3]});

        // Добавление пользователя в хэш онлайн игроков в redis
        redis.hset(REDIS_CHARACTERS_HASH, clientData[1], userDataJson);
    }
});

// Событие при отключении удаленной процедуры
app.on('RPCUnregistered', function (topicUri) {
   var channel = topicUri[0];

   if (channel.lastIndexOf('online.', 0) === 0) {
       var clientData = channel.split(".");

       // удаление пользователя из хэша онлайн игроков в redis
       redis.hdel(REDIS_CHARACTERS_HASH, clientData[1]);
   }
});

console.log('WebSocket router is running ...');
