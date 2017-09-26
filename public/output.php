<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($_GET['filename'])) {
    echo 'Не удалось получить имя файла.';
    exit();
}

if (empty($_GET['filename'])) {
    echo 'Имя файла не задано.';
    exit();
}

$downloader = new ThreadDownloader;
$downloader->outputsFile($_GET['filename']);