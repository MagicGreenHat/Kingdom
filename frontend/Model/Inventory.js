/**
 * Инвентарь персонажа (все одетые и неодетые предметы)
 */
Kingdom.Inventory = (function () {
    var items = new $.Deferred();

    return {
        /**
         * Все предметы персонажа
         * @returns object
         */
        getItems: function () {
            return items;
        },

        /**
         * Заполнение инвентаря предметами
         * @param inventoryItems
         */
        setItems: function (inventoryItems) {
            items.items = inventoryItems;
            items.resolve();
        }
    }
})();
