// Verbose output
WAMPRT_TRACE = true;
WEBSOCKET_PORT = 7777;

var Router = require('wamp.rt');
var program = require('commander');

program.option('-p, --port <port>', 'Server IP port', parseInt, WEBSOCKET_PORT);

function onPublish(topicUri, args) {
    console.log('[' + topicUri + ']: ', args);
}

// WebSocket server
var app = new Router(
    {
        port: program.port,
        handleProtocols: function (protocols, cb) {
            console.log(protocols);
            cb(true, protocols[0]);
        }
    }
);

app.on('Publish', onPublish);

console.log('WebSocket router is running ...');
