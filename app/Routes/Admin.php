<?php

use Fortuna\Page;
use Lazer\Classes\Database as Lazer;
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
                'descricao' => 'Descrição do produto 1',
                'valor'    => 10,
                'status'   => 'ATIVO'
            ]
        ]
    ]);

    return $response->withStatus(200);
});

$app->get('/admin/produto/cadastro', function (Request $request, Response $response, $args) use ($resource_path) {
    #UsuarioAdmin::checkLogin();

    $page = new Page([
        'header' => false,
        'data' => [
            'site_titulo' => 'Cadastrar Produto',
            'res_path'    => $resource_path
        ]
    ], '/views/admin/');

    $page->setTpl("admin_produto_cadastro", []);

    return $response->withStatus(200);
});

$app->post('/admin/produto/cadastro', function (Request $request, Response $response, $args) use ($resource_path) {
    #UsuarioAdmin::checkLogin();

    try {
        $produto = new Produto();

        if (!$produto->save()) {
            throw new \Exception('Erro ao salvar produto', 7400);
        }

        $response->getBody()->write(json_encode([
            'status'  => 'success',
            'message' => 'Produto cadastrado com sucesso!'
        ]));
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }


    return $response->withStatus(200);
});