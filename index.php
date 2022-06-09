<?php
namespace Fortuna;

use Slim\Factory\AppFactory;

require_once("vendor/autoload.php");

session_start();

$app = AppFactory::create();

#$app->config('debug', true);

require_once("Controller/UsuarioController.php");

$app->run();