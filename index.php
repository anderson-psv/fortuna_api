<?php

ini_set("error_log", "/log/php-error.log");
define('LAZER_DATA_PATH', realpath(__DIR__) . '/database/'); //Path to folder with tables



use Fortuna\Logger;
use Fortuna\Db\FileDb;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;

require_once 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

putenv('DEBUG=1');

$file_path = __DIR__ . '/database/f_usuario.data.json';

FileDb::createAllTables(__DIR__ . '/conf/database_ini.json');

if ($files = opendir('app/Routes/')) {

    $app = Slim\Factory\AppFactory::create();

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    //Middleware
    $app->add(function (Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);
        $request
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Authorization, Origin, Accept')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');

        if (getenv('DEBUG') == 1) {
            (new Logger)->debug('New Request', [
                'method'  => $request->getMethod(),
                'path'     => (string) $request->getUri()->getPath(),
                #'headers' => $request->getHeaders(),
                #'body'    => $request->getBody()->getContents()
            ]);
        }

        return $response;
    });

    while ($file = readdir($files)) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            require "app/Routes/$file";
        }
    }

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
        throw new HttpNotFoundException($request);
    });
    
    $app->run();
    exit;
}

$page = new Fortuna\Page();
$page->setTpl('site_indisponivel');
