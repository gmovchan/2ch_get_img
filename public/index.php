<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'parse':

            if (isset($_POST["input2chLink"])) {
                echo 'Start parse.';
                $parser = new Parser2ch();
                echo '<br>';
                $parser->parseThread($_POST["input2chLink"]);                
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
