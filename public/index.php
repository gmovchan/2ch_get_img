<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'parse':

            if (isset($_POST["input2chLink"])) {
                echo 'Start parse.';
                $parser = new ThreadParser;
                echo '<br>';
                $code = $parser->parseThread(new ThreadDownloader, $_POST["input2chLink"]);
                echo '<hr>';
                echo htmlspecialchars($code);
            } else {
                echo 'Ссылка не получена.';
            }

            exit();
            break;

        default:

            break;
    }
}

require __DIR__ . '/../app/index_template.php';
