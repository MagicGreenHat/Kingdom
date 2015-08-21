/**
 * Персонаж
 */
Kingdom.User = (function () {
    var username;
    var avatar;

    return {
        create: function (name, userAvatar) {
            username = name;
            avatar = userAvatar;
        },

        /**
         * Имя персонажа
         * @returns string
         */
        getName: function () {
            return username;
        },

        /**
         * Аватар персонажа
         * @returns string
         */
        getAvatar: function () {
            return avatar;
        },

        /**
         * Отрисовка аватара
         * @param avatar
         */
        renderAvatar: function (avatar) {
            $('#game-user-info').find('.avatar').html('<img src="' + avatar + '">');
        }
    }
})();

