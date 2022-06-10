<?php

use Fortuna\Controller\UsuarioController;

require_once '../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();
$app->setBasePath('/fortuna');
UsuarioController::getRoutes($app);
$app->run();