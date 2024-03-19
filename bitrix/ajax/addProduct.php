<?php

use Bitrix\Catalog\Product\Basket;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$postData = Context::getCurrent()->getRequest()->getPostList()->toArray();

if (!$postData['id'] || !$postData['quantity']) {
    echo 'Произошла ошибка при отправке данных';
    return;
}


try {
    Loader::includeModule('catalog');
} catch (LoaderException $e) {
    echo 'Произошла ошибка на сервере';
    return;
}

$fields = [
    'PRODUCT_ID' => $postData['id'],
    'QUANTITY' => $postData['quantity'],
];

$result = Basket::addProduct($fields);

if ($result->isSuccess()) {
    echo 'Товар добавлен в корзину';
} else {
    echo 'Произошла ошибка: ' . implode('; ', $result->getErrorMessages());
}
