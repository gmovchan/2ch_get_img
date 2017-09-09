<?php

namespace Application;

require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case 'parse':
            echo 'Start parse';
            exit();
            break;

        default:
            
            break;
    }
}

require __DIR__ . '/../app/index_template.php';