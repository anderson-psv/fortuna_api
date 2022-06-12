<?php

define('LAZER_DATA_PATH', realpath(__DIR__) . '/database/'); //Path to folder with tables

use Fortuna\Db\FileDb;
use Fortuna\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Lazer\Classes\Database as Lazer;

require_once 'vendor/autoload.php';

putenv('DEBUG=1');

$file_path = __DIR__ . '/database/f_usuario.data.json';

FileDb::createAllTables(__DIR__ . '/conf/database_ini.json');
$row = Lazer::table('f_usuario');

$row->set([
        'email' => 'asilva@imply.com',
        'senha' => '123456',
        'isadmin' => true
    ]);

$row->save();

$json  = file_get_contents($file_path);
$array = json_decode($json, true);

echo $json;
#Lazer::table('f_usuario')->delete();
#unlink($file_path);

exit;

if ($files = opendir('app/Routes/')) {

    $app = Slim\Factory\AppFactory::create();

    if (getenv('DEBUG') == 1) {
        $app->add(function (Request $request, RequestHandler $handler) {
            $response = $handler->handle($request);
            (new Logger)->debug('New Request', [
                'method'  => $request->getMethod(),
                'path'     => (string) $request->getUri()->getPath(),
                #'headers' => $request->getHeaders(),
                #'body'    => $request->getBody()->getContents()
            ]);

            return $response;
        });
    }

    while ($file = readdir($files)) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            require_once "app/Routes/$file";
        }
    }
    $app->run();
    exit;
}

$page = new Fortuna\Page();
$page->setTpl('site_indisponivel');
