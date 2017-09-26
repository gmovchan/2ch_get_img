<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$statusArray = array(
    'statusBar' => null,
    'statusText' => null,
    'downloadingComplete' => false,
    'archiveFileName' => null
);

if (isset($_SESSION['statusBar'])) {
    $statusArray['statusBar'] = $_SESSION['statusBar'];
}

if (isset($_SESSION['statusText'])) {
    $statusArray['statusText'] = $_SESSION['statusText'];
}

if (isset($_SESSION['downloadingComplete'])) {
    $statusArray['downloadingComplete'] = true;
    $statusArray['archiveFileName'] = $_SESSION['archiveFileName']; // Имя архива для скачивания

    // Сбрасывает значения переменных сессии чтобы они не помешали при новом запуске скрипта
    unset($_SESSION['downloadingComplete']);
    unset($_SESSION['statusBar']);
    unset($_SESSION['archiveFileName']);
}

$statusJSON = json_encode($statusArray, JSON_UNESCAPED_UNICODE);

echo $statusJSON;
