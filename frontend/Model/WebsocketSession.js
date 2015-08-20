/**
 * Websocket сессия
 * @param session Сессия autobahn.js
 * @param sessionId Id симфони-сессии
 * @constructor
 */
var WebsocketSession = function (session, sessionId) {
    this.session = session;
    this.localChannel = 'character.' + sessionId;
    this.onlineRPC = 'online.' + sessionId;
};

WebsocketSession.prototype = {
    session: function () {
        return this.session;
    },
    localChannel: function () {
        return this.localChannel;
    },
    onlineRPC: function () {
        return this.onlineRPC;
    },
    command: function () {

    }
};
