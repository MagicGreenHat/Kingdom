WAMPRT_TRACE = true; // Verbose output
WEBSOCKET_PORT = 7777;

var Router = require('wamp.rt');

// WebSocket router
var app = new Router({port: WEBSOCKET_PORT});

app.on('Publish', function (topicUri, args) {
    console.log('[' + topicUri + ']:', args[0]);
});

console.log('WebSocket router is running ...');
