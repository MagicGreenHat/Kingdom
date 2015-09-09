$(function () {
    $('#game-room').on('click', '.resource-obtain.wood.button', function () {
        Kingdom.Websocket.command('obtainWood');
        Kingdom.Chat.addInfo('Ты рубишь дерево');
    });
});
