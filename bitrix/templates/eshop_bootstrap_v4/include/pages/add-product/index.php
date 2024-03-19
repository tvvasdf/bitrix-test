<?php

use Bitrix\Main\Context;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

if (!defined('BASE_PRICE_ID')) {
    define('BASE_PRICE_ID', 1);
}

$query = trim(Context::getCurrent()->getRequest()->getQuery('art'));
$result = false;

if ($query) {
    $rsData = CIBlockElement::GetList(
        [],
        [
            'IBLOCK_TYPE' => 'offers',
            'IBLOCK_CODE' => 'clothes_offers',
            'ACTIVE' => 'Y',
            '=AVAILABLE' => 'Y',
            'PROPERTY_ARTNUMBER' => $_GET['art']
        ],
        false,
        false,
        [
            'ID',
            'NAME',
            'PRICE_' . BASE_PRICE_ID,
            'QUANTITY'
        ]
    );
    $result = $rsData->Fetch();
}
?>

<form action="">
    <label>
        Введите код товара:
        <input type="text" name="art" />
    </label>
    <input type="submit" />
</form>

<hr>

<?php if (!$result): ?>
    <p>Товаров с кодом <b><?= $query ?></b> не найдено</p>
<?php else: ?>
    <div class="container">
        <p>Название: <?= $result['NAME'] ?></p>
        <p>Цена: <?= $result['PRICE_' . BASE_PRICE_ID] ?></p>
        <label>
            Количество (max: <?= $result['QUANTITY'] ?>):
            <input data-product-quantity="" type="number" max="<?= $result['QUANTITY'] ?>" min="1" value="1" />
        </label>
        <button data-product-add="<?= $result['ID'] ?>">Добавить в корзину</button>
    </div>
<?php endif;?>
