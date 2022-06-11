<?php


require_once 'vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();

$files = opendir('app/Routes/');

while ($file = readdir($files)) {
    if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        require_once "app/Routes/$file";
    }
}


$app->run();
