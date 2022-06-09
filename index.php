<?php
namespace Fortuna;

use \Slim\Slim;

session_start();

require_once("vendor/autoload.php");

$app = new Slim();

$app->config('debug', true);

require_once("Controller/UsuarioController.php");

$app->run();