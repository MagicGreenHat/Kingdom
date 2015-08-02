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
        var localChannelName = 'character.' + data.sessionId;

        // Получение данных о пользователе из redis
        redis.hget('kingdom:characters:hash', data.sessionId, function (err, characterDataJson) {
            var character = JSON.parse(characterDataJson);

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
                        if (command == 'chat') {
                            var chatData = {
                                chat: {
                                    from: character.name,
                                    phrase: commandArguments
                                }
                            };

                            sendToOnlinePlayers(chatData);
                        } else {
                            runConsoleCommand(character, command);
                        }
                    } else {
                        console.log('[' + localChannelName + ']: ' + localResponse);
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

                function sendToOnlinePlayers(message) {
                    var messageJson = JSON.stringify(message);

                    session.subscriptions.forEach(function (subscription) {

                        session.publish(subscription[0].topic, [messageJson]);
                    });
                }

            }

            sendToOnlinePlayers({info: {event: 'playerEnter', name: character.name}});

            return data;
        });
    });
};

connection.open();
