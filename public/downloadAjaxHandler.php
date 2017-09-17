<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

$resultMessage = "";
$responseHandler = ResponseHandler::getInstance();
$data = array('result' => null,);

if (isset($_POST["link"])) {
    $parser = new ThreadParser;
    $code = $parser->parseThread(new ThreadDownloader, $_POST["link"]);

    if ($code) {
        $data['result']['success'] = $responseHandler->getSuccessArray();

        if (!empty($data['result']['success'])) {
            echo '<p>Успех:</p>';
            foreach ($data['result']['success'] as $success) {
                echo "<p>$success</p>";
            }
        }
    } else {
        $data['result']['errors'] = $responseHandler->getErrorsArray();

        if (!empty($data['result']['errors'])) {
            echo '<p>Ошибки:</p>';
            foreach ($data['result']['errors'] as $error) {
                echo "<p>$error</p>";
            }
        }
    }
} else {
    $responseHandler->addError("Не удалось получить ссылку из формы");
    $data['result']['errors'] = $responseHandler->getErrorsArray();
    
    if (!empty($data['result']['errors'])) {
            echo '<p>Ошибки:</p>';
            foreach ($data['result']['errors'] as $error) {
                echo "<p>$error</p>";
            }
        }
}