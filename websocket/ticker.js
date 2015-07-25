/**
 * Сервер для отсчета времени
 */

SECONDS_IN_TICK = 60;

var autobahn = require('autobahn');

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777/',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    var tick = 1;
    function processTick() {
        session.publish('system', ['tick #' + tick]);
        tick++;
    }

    processTick();
    setInterval(processTick, SECONDS_IN_TICK * 1000);

};

connection.open();

