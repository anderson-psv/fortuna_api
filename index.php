<?php

use Fortuna\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once 'vendor/autoload.php';

putenv('DEBUG=1');

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
