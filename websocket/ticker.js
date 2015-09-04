/**
 * Сервер для отсчета времени
 */

var config = require('./config/config.json');
var autobahn = require('autobahn');
var redis = require('then-redis').createClient();

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777',
    realm: 'kingdom'
});

connection.onopen = function (session) {
    console.log('Ticker server is running ...');

    var sendToAllOnlinePlayers = function (message) {
        redis.hgetall(config.redisIdSessionHash).then(function (sessions) {
            for (var property in sessions) {
                if (sessions.hasOwnProperty(property)) {
                    var channel = 'character.' + sessions[property];
                    var messageJson = JSON.stringify(message);

                    session.publish(channel, [messageJson]);
                }
            }
        });
    };

    function gameAdvice() {
        var advices = [
            'Чтобы узнать, что должно случиться, достаточно проследить, что было.',
            'За золото не всегда найдешь хороших солдат, а хорошие солдаты всегда достанут золото.',
            'Цель оправдывает средства.',
            'У победителя много друзей, и лишь у побежденного они настоящие.',
            'Скрой то, что говоришь сам, узнай то, что говорят другие, и станешь истинным князем.',
            'Войны начинаются когда захотят, но завершаются, когда могут.'
        ];

        return {
            info: {
                event: 'advice',
                advice: advices[Math.floor(Math.random() * advices.length)]
            }
        };
    }

    var oldAdvice = {info: {advice: ''}};
    setInterval(function () {
        var advice = gameAdvice();

        if (oldAdvice.info.advice != advice.info.advice) {
            sendToAllOnlinePlayers(advice);
            oldAdvice = advice;
        }

    }, config.secondsToAdvice * 1000);

};

connection.open();
