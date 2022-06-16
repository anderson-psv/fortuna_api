<?php

use Fortuna\Page;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$resource_path = './../../res/admin/';

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

$app->get('/admin/produtos', function (Request $request, Response $response, $args) use ($resource_path) {
    #UsuarioAdmin::checkLogin();

    $page = new Page([
        'header' => false,
        'data' => [
            'site_titulo' => 'Produtos',
            'res_path'    => $resource_path
        ]
    ], '/views/admin/');

    $page->setTpl("admin_produtos", [
        'produtos' => [
            0 => [
                'id'       => 1,
                'nome'     => 'Produto 1',
                'descricao' => 'Descrição do produto 1',
                'valor'    => 10,
                'status'   => 'ATIVO'
            ]
        ]
    ]);

    return $response->withStatus(200);
});
