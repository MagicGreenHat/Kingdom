/**
 * Websocket сессия
 */
Kingdom.Websocket = (function () {
    var session;
    var localChannel;

    return {

        /**
         * Сохранение сессии и регистрация удаленной процедуры для отслеживания дисконнекта
         * @param websocketSession Сессия autobahn.js
         * @param symfonySessionId Id symfony-сессии
         */
        register: function (websocketSession, symfonySessionId) {
            session = websocketSession;
            localChannel = 'character.' + symfonySessionId;

            session.register('online.' + symfonySessionId, function () {});
        },

        /**
         * @returns session
         */
        getSession: function () {
            return session;
        },

        /**
         * Личный вебсокет-канал игрока
         * @returns string
         */
        getChannel: function () {
            return localChannel;
        },

        /**
         * Название RPC-канала для отслеживания онлайн-статуса игроков
         * @returns string
         */
        getOnline: function () {
            return onlineHandler;
        },

        /**
         * Отправка команды по локальному каналу
         * @param command
         * @param arguments string|array
         */
        command: function (command, arguments) {
            if (typeof arguments != 'undefined' && arguments.constructor === Array) {
                arguments = arguments.join(':');
            }

            session.publish(localChannel, [{command: command, arguments: arguments}]);
        }
    }
})();
