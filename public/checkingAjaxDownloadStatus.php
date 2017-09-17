<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$statusArray = array(
    'statusBar' => '',
    'downloadingComplete' => false
);

if (isset($_SESSION['statusBar'])) {
    $statusArray['statusBar'] = $_SESSION['statusBar'];
} else {
    $statusArray['statusBar'] = "Нет данных.";
}

if (isset($_SESSION['downloadingComplete'])) {
    $statusArray['downloadingComplete'] = true;

    // Сбрасывает значения переменных сессии чтобы они не помешали при новом запуске скрипта
    unset($_SESSION['downloadingComplete']);
    unset($_SESSION['statusBar']);
}

$statusJSON = json_encode($statusArray, JSON_UNESCAPED_UNICODE);

echo $statusJSON;
