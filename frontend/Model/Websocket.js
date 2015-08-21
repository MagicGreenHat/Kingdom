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
         * @param arguments строка, или несколько аргументов разделенных символом :
         */
        command: function (command, arguments) {
            //TODO[Rottenwood]: if (typeof arguments == 'object') { // implode }

            session.publish(localChannel, [{command: command, arguments: arguments}]);
        }
    }
})();
