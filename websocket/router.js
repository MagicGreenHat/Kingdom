WAMPRT_TRACE = true; // Verbose output
WEBSOCKET_PORT = 7777;

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

// Событие при отключении удаленной процедуры
app.on('RPCUnregistered', function (topicUri) {
   var channel = topicUri[0];

   if (channel.lastIndexOf('online.', 0) === 0) {
       var sessionId = channel.replace(/online\./g, '');

       // удаление пользователя из хэша онлайн игроков в redis
       redis.hdel('kingdom:characters:hash', sessionId);
   }
});

console.log('WebSocket router is running ...');
