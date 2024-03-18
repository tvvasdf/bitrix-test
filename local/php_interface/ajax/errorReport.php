<?php

use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$postData = Context::getCurrent()->getRequest()->getPostList()->toArray();

if (!$postData['text'] || !$postData['sess_id'] || !$postData['url']) {
    return;
}

const COOLDOWN_TIME = 15;
const TABLE_NAME = 'error_reporting_table';
const EVENT_NAME = 'ERROR_REPORT';

try {
    $id = getHlId(TABLE_NAME);
    $entity = HL::compileEntity(HL::getById($id)->fetch())->getDataClass();
    Loader::IncludeModule('highloadblock');
} catch (SystemException|LoaderException $e) {
    echo 'Произошла ошибка на сервере';
}


$rs = $entity::getList([
    'limit' => 1,
    'order' => [
        'UF_TIME' => 'DESC'
    ],
    'filter' => [
        'UF_SESS_ID' => $_POST['sess_id'],
    ],
    'select' => [
        'UF_TIME',
    ],
]);

if ($ar = $rs->fetch()) {
    if ($ar['UF_TIME'] + COOLDOWN_TIME > time()) {
        echo 'Следующее сообщение можно отправить через ' . $ar['UF_TIME'] + COOLDOWN_TIME - time() . ' с.';
        return;
    }
}

$data = [
    'UF_SESS_ID' => $postData['sess_id'],
    'UF_TIME' => time(),
    'UF_URL' => $postData['url'],
    'UF_TEXT' => $postData['text'],
];

$result = $entity::add($data);

if ($result->isSuccess()) {
    echo 'Ваше сообщение успешно отправлено';

    Event::send([
        'EVENT_NAME' => EVENT_NAME,
        'LID' => SITE_ID,
        'C_FIELDS' => $data
    ]);
} else {
    echo 'Произошла ошибка: ' . implode('; ', $result->getErrorMessages());
}