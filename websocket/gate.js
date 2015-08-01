/**
 * Сервер для прослушки командного канала и запуска команд
 */

COMMAND_CHANNEL_NAME = 'command';
SYSTEM_CHANNEL_NAME = 'system';
GATE_CHANNEL_NAME = 'gate';
SYMFONY_CONSOLE_ENTRY_POINT = '../app/console kingdom:execute';

var autobahn = require('autobahn');
var exec = require('child_process').exec;
var redis = require("redis").createClient();

redis.on('error', function (err) {
    console.log('[!] Redis error ' + err);
});


var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

connection.onopen = function (session) {
    session.publish(SYSTEM_CHANNEL_NAME, ['Gate service is running ...']);

    //TODO[Rottenwood]: Удаленная команда всем клиентам переподключиться, чтобы гейт подключился к локальным каналам

    //TODO[Rottenwood]: Отключаться от каналов, когда из них выходят клиенты

    session.register(GATE_CHANNEL_NAME, function (args) {
        var data = args[0];
        var localChannelName = 'character.' + data.hash;
        var hash = data.hash;

        var isLocalChannelSubscribed = session.subscriptions.some(function (subscription) {
            return subscription[0].topic == localChannelName;
        });

        if (!isLocalChannelSubscribed) {
            session.subscribe(localChannelName, function (args) {
                var localResponse = args[0];
                var command = localResponse.command;
                var commandArguments = localResponse.arguments;

                if (command) {
                    console.log('[' + localChannelName + '] [команда]: ' + command);

                    // Получение данных о пользователе из redis
                    redis.hget('kingdom:characters:hash', hash, function (err, characterDataJson) {
                        var character = JSON.parse(characterDataJson);

                        runConsoleCommand(character, command);

                        if (command == 'north') {
                            session.publish(localChannelName, ['Вы отправились на север']);
                            session.publish(localChannelName, [{map: {a1: 1, a2: 2, a3: 3}}]);
                        } else if (command == 'south') {
                            session.publish(localChannelName, ['Вы отправились на юг']);
                            session.publish(localChannelName, [{map: {a1: 3, a2: 2, a3: 1}}]);
                        } else if (command == 'chat') {
                            session.subscriptions.forEach(function (subscription) {
                                session.publish(subscription[0].topic, [{
                                    chat: {from: character.name, phrase: commandArguments}
                                }]);
                            });
                        }
                    });
                } else {
                    console.log('[' + localChannelName + '] [чат]: ' + localResponse);
                }

                // Запуск консольной команды
                function runConsoleCommand(character, command) {
                    var cmd = SYMFONY_CONSOLE_ENTRY_POINT + ' ' + character.id + ' ' + command;


                    exec(cmd, function (error, stdout) {

                        //TODO[Rottenwood]: Обработка ошибок
                        if (error) {
                            console.log(error);
                        }

                        session.publish(localChannelName, [stdout]);
                    });
                }
            });
        }

        return data;
    });
};

connection.open();
