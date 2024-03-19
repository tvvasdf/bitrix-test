BX.ready(function () {
    keyboardInput();
    addProduct();
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
                '/bitrix/ajax/errorReport.php',
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

function addProduct() {
    document.querySelector('[data-product-add]').addEventListener('click', e => {
        e.preventDefault();

        const id = document.querySelector('[data-product-add]').getAttribute('data-product-add');
        const quantity = document.querySelector('[data-product-quantity]').value;


        let data = {
            id: id,
            quantity: quantity
        };

        BX.ajax.post(
            '/bitrix/ajax/addProduct.php',
            data,
            r => alert(r)
        );
    });
}