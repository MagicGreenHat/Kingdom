/**
 * Удержание фокуса курсора на поле чата
 */
$(function () {
    $('body').on('click', function () {
        $('#chat-input').focus();
    });
});
