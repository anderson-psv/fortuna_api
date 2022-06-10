<?php

use Fortuna\Controller\UsuarioController;

require_once '../vendor/autoload.php';
require_once __DIR__ . '/../app/bootstrap/bootstrap.php';

$app = Slim\Factory\AppFactory::create();
$app->setBasePath("/fortuna_api/index.php");
$app = UsuarioController::getRoutes($app);
$app->run();