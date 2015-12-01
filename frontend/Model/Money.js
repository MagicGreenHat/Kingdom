/**
 * Деньги персонажа
 */
Kingdom.Money = (function () {
    var money = new $.Deferred();

    return {
        setMoney: function (moneyData) {
            if (typeof (moneyData) == 'undefined') {
                money.gold = 0;
                money.silver = 0;
            } else {
                money.gold = moneyData.gold;
                money.silver = moneyData.silver;
            }

            money.resolve();
        },

        /**
         * Деньги
         * @returns object
         */
        getMoney: function () {
            return money;
        }
    }
})();
