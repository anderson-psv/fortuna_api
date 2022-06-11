<?php


require_once '../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();

$files = opendir(dirname(__DIR__) . '/app/Routes/');

while ($file = readdir($files)) {
    if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        require_once dirname(__DIR__) . "/app/Routes/$file";
    }
}


$app->run();
