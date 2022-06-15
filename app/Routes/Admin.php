<?php

use Fortuna\Page;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$resource_path = './../../vendor/almasaeed2010/adminlte/';

$app->get('/admin', function (Request $request, Response $response, $args) use ($resource_path) {
    $page = new Page([
        'data' => [
            'site_titulo' => 'Login',
            'res_path'    => $resource_path
        ]
    ], '/views/admin/');

    $page->setTpl("index");

    return $response->withStatus(200);
});
