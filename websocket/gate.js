/**
 * Сервер для прослушки командного канала и запуска команд
 */

COMMAND_CHANNEL_NAME = 'command';
SYMFONY_CONSOLE_PATH = '../app/console';

var autobahn = require('autobahn');
var exec = require('child_process').exec;

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    session.subscribe(COMMAND_CHANNEL_NAME, function onevent(args) {
        var cmd = SYMFONY_CONSOLE_PATH + ' --command ' + args[0];

        exec(cmd, function (error, stdout, stderr) {
            session.publish(COMMAND_CHANNEL_NAME, [stdout]);
        });
    });

    session.publish(COMMAND_CHANNEL_NAME, ['Gate service']);
};

connection.open();
