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
         * @param inventoryItems object
         */
        setItems: function (inventoryItems) {
            items.items = inventoryItems;
            items.resolve();
        },

        /**
         * Удаление предмета из инвентаря
         * @param itemId int
         */
        removeItem: function (itemId) {
            items.items.forEach(function (item, key) {
                if (item.itemId == itemId) {
                    items.items.splice(key, 1);
                }
            });
        }
    }
})();
