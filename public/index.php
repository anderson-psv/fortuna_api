<?php
use Fortuna\Controller\UsuarioController;

require_once '../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();
UsuarioController::getRoutes($app);
$app->run();