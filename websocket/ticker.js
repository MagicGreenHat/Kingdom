/**
 * Сервер для отсчета времени
 */

SECONDS_IN_TICK = 60;
REDIS_ID_SESSION_HASH = 'kingdom:users:sessions';
SECONDS_TO_ADVICE = 600;

var autobahn = require('autobahn');
var redis = require('then-redis').createClient();

var connection = new autobahn.Connection({
    url: 'ws://localhost:7777',
    realm: 'kingdom'
});

connection.onopen = function (session) {

    console.log('Ticker server is running ...');

    var sendToAllOnlinePlayers = function (message) {
        redis.hgetall(REDIS_ID_SESSION_HASH).then(function (sessions) {
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
            'За золото не всегда найдёшь хороших солдат, а хорошие солдаты всегда достанут золото.',
            'Цель оправдывает средства.',
            'У победителя много друзей, и лишь у побежденного они настоящие.',
            'Скрой то, что говоришь сам, узнай то, что говорят другие, и станешь истинным князем.',
            'Войны начинают когда хотят, но завершают, когда могут.'
        ];

        return {
            info: {
                event: 'advice',
                advice: advices[Math.floor(Math.random() * advices.length)]
            }
        };
    }

    setInterval(function () {
        sendToAllOnlinePlayers(gameAdvice());
    }, SECONDS_TO_ADVICE * 1000);

};

connection.open();
