define(['jquery'], function ($) {
    return (function () {
        var $money = $('#game-inventory .money');
        var money = {};

        var getMoney = function () {
            var html = '<div>Золото: ' + money.gold + '</div>'
                + '<div>Серебро: ' + money.silver + '</div>';

            $money.html(html);
        };

        return {
            init: function () {
                $('.open-inventory-button').click(function () {
                    getMoney();
                });
            },

            setMoney: function (moneyData) {
                if (typeof (moneyData) == 'undefined') {
                    money.gold = 0;
                    money.silver = 0;
                } else {
                    money.gold = moneyData.gold;
                    money.silver = moneyData.silver;
                }
            }
        }
    })();
});
