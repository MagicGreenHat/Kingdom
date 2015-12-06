/**
 * Инвентарь персонажа (все надетые и ненадетые предметы)
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
         * Надевание предмета
         * @param itemId int
         * @param slot string
         */
        wearItem: function (itemId, slot) {
            items.items.forEach(function (item, key, itemsArray) {
                if (item.itemId == itemId) {
                    itemsArray[key]['slot'] = slot;
                }
            });
        },

        /**
         * Снятие предмета
         * @param itemId int
         */
        removeItem: function (itemId) {
            items.items.forEach(function (item, key, itemsArray) {
                if (item.itemId == itemId) {
                    delete itemsArray[key]['slot'];
                }
            });
        }
    }
})();
