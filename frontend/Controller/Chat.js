/**
 * Удержание фокуса курсора на поле чата
 */
$(function () {
    var $chatInput = $('#chat-input');

    $('body').on('click', function () {
        $chatInput.focus();
    });

    /**
     * При клике на название комнаты оно добавляется в сообщение чата
     */
    $('#game-room').on('click', '.room-name', function () {
        $chatInput.val($chatInput.val() + ' ' + Kingdom.Room.getName());
    });
});
