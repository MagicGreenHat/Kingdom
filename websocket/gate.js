/**
 * Сервер для запуска симфони команд
 */

var config = require('./config/config.json');
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
    console.log('Gate service is running ...');
    reloadAllClients();
    initLogger();

    session.register(config.gateChannelName, function (args) {
        var data = args[0];
        var localChannelName = 'character.' + data.sessionId;

        // Получение данных о пользователе из redis
        redis.hget(config.redisSessionIdHash, data.sessionId).then(function (userId) {
            redis.hget(config.redisIdUsernameHash, userId).then(function(username) {
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
                                                publishToChannel(channel, {commandName: 'moveAnother', message: message});
                                            });
                                        }

                                        if (responseData.hasOwnProperty('enter')) {
                                            responseData.enter.forEach(function(channel) {
                                                var message = responseData.name + ' пришел ' + responseData.directionFrom;

                                                publishToChannel(channel, {commandName: 'moveAnother', message: message});
                                            });
                                        }
                                    }

                                    publishToLocalChannel(commandResponseJson);
                                });
                            } else {
                                runConsoleCommand(character, command, function (commandResultJson) {
                                    if (command == 'obtainWood') {
                                        var commandResult = JSON.parse(commandResultJson);

                                        if (commandResult.data && commandResult.data.hasOwnProperty('resources')) {
                                            redis.hget(config.redisIdRoomHash, character.id).then(function (roomId) {
                                                sendToOnlinePlayersInRoom(
                                                    roomId,
                                                    {
                                                        info:
                                                        {
                                                            event: 'obtainWood',
                                                            name: character.name,
                                                            resources: commandResult.data.resources,
                                                            typeChanged: commandResult.data.typeChanged
                                                        }
                                                    }
                                                );
                                            });
                                        }
                                    }

                                    publishToLocalChannel(commandResultJson)
                                });
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
                            var cmd = config.symfonyConsoleEntryPoint + ' ' + character.id + ' ' + command;

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
                     * Отправка сообщения всем игрокам в комнате
                     * @param currentRoomId
                     * @param message
                     */
                    function sendToOnlinePlayersInRoom(currentRoomId, message) {
                        var messageJson = JSON.stringify(message);

                        redis.smembers(config.redisOnlineList).then(function (onlineUsers) {
                            onlineUsers.forEach(function (userId) {
                                if (userId != character.id) {
                                    redis.hget(config.redisIdRoomHash, userId).then(function (roomId) {
                                        if (roomId == currentRoomId) {
                                            redis.hget(config.redisIdSessionHash, userId).then(function (sessionId) {
                                                var channel = 'character.' + sessionId;

                                                session.publish(channel, [messageJson]);
                                            });
                                        }
                                    });
                                }
                            });

                        });
                    }

                    /**
                     * Запрос количества пользователей находящихся онлайн
                     */
                    function getPlayersOnline() {
                        redis.scard(config.redisOnlineList).then(function (playersOnline) {
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
                    function publishToChannel(channel, message) {
                        session.publish('character.' + channel, [JSON.stringify(message)]);
                    }
                }
            });
        });
    });

    function reloadAllClients() {
        redis.hgetall(config.redisIdSessionHash).then(function (sessions) {
            for (var property in sessions) {
                if (sessions.hasOwnProperty(property)) {
                    var channel = 'character.' + sessions[property];
                    var messageJson = JSON.stringify({commandName: 'reloadPage'});

                    session.publish(channel, [messageJson]);
                }
            }
        });
    }

    function initLogger() {
        session.subscribe(config.logChannel, function (jsonData) {
            var data = JSON.parse(jsonData[0]);
            var event = data.event;
            var userId = data.userId;
            var userName = data.userName;

            if (event == 'playerEnter' || event == 'playerExit') {
                var cmd = config.symfonyConsoleLogCommand + ' ' + event + ' ' + userId + ' ' + userName;

                exec(cmd, function (error) {
                    if (error) {
                        console.log(error);
                    }
                });
            }
        });

    }
};

connection.open();
