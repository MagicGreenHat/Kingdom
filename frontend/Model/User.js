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

        getName: function () {
            return username;
        },

        /**
         *
         * @returns {*}
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

