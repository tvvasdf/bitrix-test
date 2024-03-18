BX.ready(function () {
    keyboardInput();
});

function keyboardInput() {
    BX.bindDelegate(
        document, 'keydown', null,
        e => {
            let bool, text;
            const enterCode = 13;

            if (!e.ctrlKey || e.keyCode !== enterCode) {
                return;
            }

            text = window.getSelection().toString();
            if (text.length === 0) {
                return;
            }

            bool = confirm(`Вы уверены, что хотите отправить сообщение об ошибке? Текст с ошибкой: "${text}"`);
            if (!bool) {
                return;
            }

            BX.ajax.post(
                '/local/php_interface/ajax/errorReport.php',
                {
                    url: window.location.pathname,
                    text: text,
                    sess_id: BX.bitrix_sessid()
                },
                r => alert(r)
            );
        }
    );
}