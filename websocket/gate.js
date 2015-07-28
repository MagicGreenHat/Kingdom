/**
 * Сервер для прослушки командного канала и запуска команд
 */

COMMAND_CHANNEL_NAME = 'command';
SYSTEM_CHANNEL_NAME = 'system';
GATE_CHANNEL_NAME = 'gate';
SYMFONY_CONSOLE_PATH = '../app/console';

var autobahn = require('autobahn');
var exec = require('child_process').exec;

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    //session.subscribe(COMMAND_CHANNEL_NAME, function onevent(args) {
    //    var cmd = SYMFONY_CONSOLE_PATH + ' --command ' + args[0];
    //
    //    exec(cmd, function (error, stdout, stderr) {
    //        session.publish(COMMAND_CHANNEL_NAME, [stdout]);
    //    });
    //});

    session.publish(SYSTEM_CHANNEL_NAME, ['Gate service is running ...']);

    session.register(GATE_CHANNEL_NAME, function (args) {
        var data = args[0];

        var localChannelName = 'character.' + data.session;

        session.subscribe(localChannelName, function (args) {
            var data = args[0];
            var command = data.command;
            var commandArguments = data.arguments;

            if (command) {
                console.log('[' + localChannelName + '] [команда]: ' + command);

                //TODO: Запуск симфони-команды
                if (command == 'north') {
                    session.publish(localChannelName, [{map: {a1: 1, a2: 2, a3: 3}}]);
                } else if (command == 'south') {
                    session.publish(localChannelName, [{map: {a1: 3, a2: 2, a3: 1}}]);
                }

            } else {
                console.log('[' + localChannelName + '] [чат]: ' + data);
            }

        });

        session.publish(localChannelName, ['TEST']);

        return data;
    });

};

connection.open();
