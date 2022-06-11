<?php

use Fortuna\Page;

require_once 'vendor/autoload.php';

if(!$files = opendir('app/Routes/')) {

    $app = Slim\Factory\AppFactory::create();
    while ($file = readdir($files)) {
        if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            require_once "app/Routes/$file";
        }
    }

    $app->run();
    exit;
}

$page = new Page();
$page->setTpl('site_indisponivel');
