$(function () {
    var connection = new autobahn.Connection({
        url: 'ws://' + window.location.hostname + ':7777', // параметр передается из twig-шаблона
        realm: 'kingdom'
    });

    connection.onopen = function (session) {
        var userData = {
            sessionId: sessionId // параметр передаeтся из twig-шаблона
        };

        // Запись вебсокет-сессии
        Kingdom.Websocket.register(session, sessionId);

        /**
         * Вызов удаленной процедуры
         */
        session.call('gate', [userData]).then(
            function () {
                var localChannelName = 'character.' + sessionId;

                session.subscribe(localChannelName, function (args) {
                    console.log(args[0]);

                    var data = JSON.parse(args[0]);

                    if (data.waitstate) {
                        Kingdom.Chat.addInfo({
                            event: 'warning',
                            message: 'Нужно отдохнуть еще ' + data.waitstate + ' ' + Kingdom.Chat.pluralize(data.waitstate, 'секунду', 'секунды', 'секунд')
                        });

                        return false;
                    }

                    //TODO[Rottenwood]: Убрать блок обработки в новый файл (напр. commandHandler.js)
                    // Обработка результатов запрошенных команд
                    if (data.commandName == 'move') {
                        if (!data.errors) {
                            Kingdom.Websocket.command('composeMap');
                        } else {
                            data.errors.forEach(function (error) {
                                Kingdom.Chat.addInfo({event: 'warning', message: error});
                            });
                        }
                    } else if (data.commandName == 'composeMap') {
                        renderRoom(data.data);
                        Kingdom.Websocket.command('showPlayersInRoom');
                    } else if (data.commandName == 'showPlayersInRoom') {
                        showPlayersInRoom(data.data);
                    } else if (data.commandName == 'moveAnother') {
                        Kingdom.Chat.addInfo(data.message);
                        Kingdom.Websocket.command('showPlayersInRoom');
                    } else if (data.commandName == 'reloadPage') {
                        location.reload();
                    } else if (data.commandName == 'inventory') {
                        if (data.data) {
                            Kingdom.Inventory.setItems(data.data);
                        }
                    } else if (data.commandName == 'getMoney') {
                        Kingdom.Money.setMoney(data.data);
                    } else if (data.commandName == 'lookUser') {
                        Kingdom.User.renderAvatar(data.data.avatar);
                    } else if (data.commandName == 'obtainWood') {
                        if (data.data.resources) {
                            Kingdom.Room.updateResources(data.data.resources);
                        }

                        if (data.data.obtained) {
                            Kingdom.Chat.addInfo('Ты рубишь дерево. Добыто древесины: ' + data.data.obtained);
                        }

                        if (data.waitstate) {
                            Kingdom.Chat.addInfo({
                                event: 'warning',
                                message: 'Нужно отдохнуть. Ты сможешь добывать древесину через ' + data.waitstate + ' ' + Kingdom.Chat.pluralize(data.waitstate, 'секунду', 'секунды', 'секунд')
                            });
                        }

                        if (data.data.typeChanged) {
                            Kingdom.Websocket.command('composeMap');
                        }
                    }

                    // Отрисовка карты
                    if (data.mapData) {
                        renderMap(data.mapData);
                    }

                    // Отрисовка чата
                    if (data.chat) {
                        Kingdom.Chat.addChatPhrase(data.chat);
                    }

                    // Вывод информационного сообщения
                    if (data.info) {
                        Kingdom.Chat.addInfo(data.info);
                    }

                    // Вывод количества игроков онлайн
                    if (data.playersOnlineCount) {
                        showOnline(data.playersOnlineCount);
                    }
                });

                // Кнопки перемещения
                $('.map .direction').on('click', function () {
                    var direction = $(this).data('direction');
                    Kingdom.Websocket.command('move', direction);
                });

                // Поле для чата
                $('#chat-input').keypress(function (event) {
                    if (event.which == 13) {
                        Kingdom.Websocket.command('chat', $(this).val());
                        $(this).val('');
                        return false;
                    }
                });

                /////// Вызов команд при загрузке страницы ///////
                Kingdom.Websocket.command('composeMap');
                Kingdom.Websocket.command('who');
                Kingdom.Websocket.command('inventory');
                Kingdom.Websocket.command('getMoney');
            }
        );
    };

    var pingInterval;
    connection.onclose = function () {
        clearInterval(pingInterval);
        ping(window.location.origin);
        pingInterval = setInterval(function () {
            ping(window.location.origin);
        }, 3000);

        var $gameOverlay = $('#game-overlay');
        var $systemMessage = $('#system-message');

        function ping(url){
            $.ajax({
                url: url,
                success: function(){
                    window.location.reload();
                },
                error: function(){
                    $gameOverlay.show();
                    $systemMessage.show();
                }
            });
        }
    };

    connection.open();

    /////// Функции клиентского интерфейса ///////

    var $gameContentRoom = $('#game-room');
    var $gameMap = $('#game-map');
    var $gameLeftBox = $('#game-left-box');

    /**
     * Отрисовка карты
     * @param mapData
     */
    function renderMap(mapData) {
        $('#game-map .map-frame img').attr('src', '/img/locations/null.png');

        mapData.forEach(function (room) {
            $('.map .y' + room.y + ' .x' + room.x)
                .html('<img src="/img/locations/' + room.pic + '.png">');
        });

        $gameLeftBox.animate({opacity: 1}, "slow");
    }

    /**
     * Отображение игроков онлайн
     * @param playersOnlineCount
     */
    function showOnline(playersOnlineCount) {
        $('#game-chat .hello-username .players-online').html('Игроков онлайн: ' + playersOnlineCount);
    }

    /**
     * //TODO[Rottenwood]: Убрать в модель Room
     * Отрисовка комнаты
     * @param roomData
     */
    function renderRoom(roomData) {
        var $roomName = $gameContentRoom.find('.room-name');
        var $roomDescription = $gameContentRoom.find('.room-description');
        var $roomPlayers = $gameContentRoom.find('.room-players');
        var $roomControls = $gameContentRoom.find('.room-actions');
        var $resourcesList = $gameContentRoom.find('.room-resources-list');

        $roomPlayers.html('');
        $roomControls.html('');
        $resourcesList.html('');

        Kingdom.Room.setData(roomData);

        $roomName.html('').html(roomData.name + '<span class="coordinates">[' + roomData.x + '/' + roomData.y + ']</span>');
        $roomDescription.html('').html(roomData.description);

        if (roomData.resources) {
            $resourcesList.html('Ресурсы в локации:');

            roomData.resources.forEach(function (resource) {
                $resourcesList.append('<div class="resource ' + resource.id + '">' + resource.name + ': <span class="quantity">' + resource.quantity + '</span></div>');
                $roomControls.append('<div class="resource-obtain ' + resource.id + ' button">Добывать ' + resource.name4 + '</div>');
            });
        }
    }

    /**
     * @Deprecated Удалить в v0.3.0
     * Обновление информации о доступных ресурсах в комнате
     * @param resourcesData
     */
    function updateResources(resourcesData) {
        $.each(resourcesData, function(resourceId, resourceQuantity) {
            var $resourceQuantity = $gameContentRoom.find('.room-resources-list .resource.' + resourceId + ' .quantity');
            $resourceQuantity.html(resourceQuantity);
        });
    }

    /**
     * Отрисовка игроков в комнате
     * @param players
     */
    function showPlayersInRoom(players) {
        var $roomPlayers = $gameContentRoom.find('.room-players');

        $roomPlayers.html('');

        players.forEach(function (player) {
            $roomPlayers.append('<div class="user-info" data-name="' + player.name + '">' + player.name + ' ' + player.stance + '</div><br>');
        });
    }

    /////// События ///////
    var $directionMapFrame = $('.map .map-frame.direction');

    $gameMap.hover(function () {
        var $directionMapFrame = $('.map .map-frame.direction');
        $directionMapFrame.toggleClass('arrow');
    });

    $gameMap.on('mousemove', function () {
        if (!$directionMapFrame.hasClass('arrow')) {
            $directionMapFrame.addClass('arrow');
        }
    });
});
