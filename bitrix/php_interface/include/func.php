<?php

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

function getHlId($table): int|false {
    Loader::IncludeModule('highloadblock');

    $rs = HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $table]
    ])->fetch();

    return $rs['ID'] ?? false;
}
