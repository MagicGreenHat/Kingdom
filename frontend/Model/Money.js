/**
 * Деньги персонажа
 */
Kingdom.Money = (function () {
    var money = {};

    return {
        setMoney: function (moneyData) {
            if (typeof (moneyData) == 'undefined') {
                money.gold = 0;
                money.silver = 0;
            } else {
                money.gold = moneyData.gold;
                money.silver = moneyData.silver;
            }
        },

        /**
         * Золотые монеты
         * @returns int
         */
        getGold: function () {
            return money.gold;
        },

        /**
         * Серебрянные монеты
         * @returns int
         */
        getSilver: function () {
            return money.silver;
        }
    }
})();
