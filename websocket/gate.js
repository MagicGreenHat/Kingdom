/**
 * Сервер для запуска симфони команд
 */

COMMAND_CHANNEL_NAME = 'command';
SYSTEM_CHANNEL_NAME = 'system';
GATE_CHANNEL_NAME = 'gate';
SYMFONY_CONSOLE_ENTRY_POINT = '../app/console kingdom:execute';

REDIS_ID_USERNAME_HASH = 'kingdom:users:usernames';
REDIS_SESSION_ID_HASH = 'kingdom:sessions:users';
REDIS_ONLINE_LIST = 'kingdom:users:online';

var autobahn = require('autobahn');
var exec = require('child_process').exec;
var redis = require('then-redis').createClient();

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777',
    realm: 'kingdom'
});

redis.on('error', function (err) {
    console.log('[!] Redis error ' + err);
});

connection.onopen = function (session) {
    session.publish(SYSTEM_CHANNEL_NAME, ['Gate service is running ...']);

    //TODO[Rottenwood]: Удаленная команда всем клиентам переподключиться, чтобы гейт подключился к локальным каналам

    //TODO[Rottenwood]: Отключаться от каналов, когда из них выходят клиенты

    session.register(GATE_CHANNEL_NAME, function (args) {
        var data = args[0];
        var localChannelName = 'character.' + data.sessionId;

        // Получение данных о пользователе из redis
        redis.hget(REDIS_SESSION_ID_HASH, data.sessionId).then(function (userId) {
            redis.hget(REDIS_ID_USERNAME_HASH, userId).then(function(username) {
                var character = {
                    id: userId,
                    name: username
                };

                var isLocalChannelSubscribed = session.subscriptions.some(function (subscription) {
                    return subscription[0].topic == localChannelName;
                });

                if (!isLocalChannelSubscribed) {
                    session.subscribe(localChannelName, function (args) {
                        var localResponse = args[0];
                        var command = localResponse.command;
                        var commandArguments = localResponse.arguments;

                        if (command) {
                            console.log('[' + localChannelName + '] [команда]: ' + command + ' [параметры]: ' + commandArguments);
                            if (command == 'chat' && commandArguments) {
                                var chatData = {
                                    chat: {
                                        from: character.name,
                                        phrase: commandArguments
                                    }
                                };

                                //TODO[Rottenwood]: Транслировать чат только в текущую комнату
                                sendToOnlinePlayers(chatData);
                            } else if (command == 'who') {
                                getPlayersOnline();
                            } else if (command == 'move') {
                                runConsoleCommand(character, command, function (commandResponseJson) {
                                    var responseData = JSON.parse(commandResponseJson).data;

                                    if (responseData) {
                                        if (responseData.hasOwnProperty('left')) {
                                            responseData.left.forEach(function(channel) {
                                                var message = responseData.name + ' ушел ' + responseData.directionTo;
                                                publichToChannel(channel, {commandName: 'moveAnother', message: message});
                                            });
                                        }

                                        if (responseData.hasOwnProperty('enter')) {
                                            responseData.enter.forEach(function(channel) {
                                                var message = responseData.name + ' пришел ' + responseData.directionFrom;

                                                publichToChannel(channel, {commandName: 'moveAnother', message: message});
                                            });
                                        }

                                        publishToLocalChannel(JSON.stringify({commandName: command}));
                                    }
                                });
                            } else {
                                runConsoleCommand(character, command, publishToLocalChannel);
                            }
                        } else {
                            console.log('[' + localChannelName + ']: ' + localResponse);
                        }

                        /**
                         * Запуск консольной команды
                         * @param character
                         * @param command
                         * @param callback
                         */
                        function runConsoleCommand(character, command, callback) {
                            var cmd = SYMFONY_CONSOLE_ENTRY_POINT + ' ' + character.id + ' ' + command;

                            if (commandArguments) {
                                cmd = cmd + ' ' + commandArguments;
                            }

                            exec(cmd, function (error, stdout) {
                                //TODO[Rottenwood]: Обработка ошибок
                                if (error) {
                                    console.log(error);
                                }

                                if (isFunction(callback)) {
                                    callback(stdout);
                                }
                            });
                        }
                    });

                    /**
                     * Отправка сообщения всем игрокам онлайн
                     * @param message
                     */
                    function sendToOnlinePlayers(message) {
                        var messageJson = JSON.stringify(message);

                        session.subscriptions.forEach(function (subscription) {
                            session.publish(subscription[0].topic, [messageJson]);
                        });
                    }

                    /**
                     * Запрос количества пользователей находящихся онлайн
                     */
                    function getPlayersOnline() {
                        redis.scard(REDIS_ONLINE_LIST).then(function (playersOnline) {
                            var jsonResponse = JSON.stringify({playersOnlineCount: playersOnline});

                            session.publish(localChannelName, [jsonResponse]);
                        });
                    }

                    /**
                     * Проверка на тип: функция
                     */
                    function isFunction(variable) {
                        return variable && typeof(variable) === 'function';
                    }

                    /**
                     * Отправка сообщения в локальный канал игрока
                     * @param message
                     */
                    function publishToLocalChannel(message) {
                        session.publish(localChannelName, [message]);
                    }

                    /**
                     * Отправка сообщения в выбранный канал игрока
                     * @param channel
                     * @param message
                     */
                    function publichToChannel(channel, message) {
                        session.publish('character.' + channel, [JSON.stringify(message)]);
                    }
                }

                sendToOnlinePlayers({info: {event: 'playerEnter', name: character.name}});
            });
        });
    });
};

connection.open();
