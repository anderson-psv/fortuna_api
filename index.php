<?php

date_default_timezone_set('America/Sao_Paulo');

session_start();
define('BASE_DIR', __DIR__);
define('LAZER_DATA_PATH', BASE_DIR . '/database/');
define('ID_HASH', '$2y$14$');

ini_set("error_log", BASE_DIR . "/log/php-error".date('Ymd').".log");

use Fortuna\Logger;
use Fortuna\Db\FileDb;
use Fortuna\Functions;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

putenv('DEBUG=1');

$file_path = BASE_DIR . '/database/f_usuario.data.json';

FileDb::createAllTables(BASE_DIR . '/conf/database_ini.json');

if ($files = opendir('app/Routes/')) {

    $app = Slim\Factory\AppFactory::create();

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    //Middleware Before
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

    //Middleware After
    $app->add(function (Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);

        $data = $response->getBody()->getContents();
        //Seta todo e qualquer header da resposta como json
        if (Functions::isJson($data)) {
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
        return $response;
    });

    $app->run();
    exit;
}

$page = new Fortuna\Page();
$page->setTpl('site_indisponivel');
