<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

$view = new View();


$responseHandler = ResponseHandler::getInstance();
$data = array('result' => null,);

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'parse':

            if (isset($_POST["link"])) {
                $parser = new ThreadParser;
                $code = $parser->parseThread(new ThreadDownloader, $_POST["link"]);
                
                if ($code) {
                    $data['result']['success'] = $responseHandler->getSuccessArray();
                } else {
                    $data['result']['errors'] = $responseHandler->getErrorsArray();
                }
                $view->generate('statusBar.php', '/indexTemplate.php', $data);
            } else {
                $responseHandler->addError("Не удалось получить ссылку из формы");
                $data['result']['errors'] = $responseHandler->getErrorsArray();
                $view->generate('statusBar.php', '/indexTemplate.php', $data);
            }

            exit();
            break;

        default:

            break;
    }
}

$view->generate('downloadFormNoAJAX.php', '/indexTemplate.php');
