<?php

use Fortuna\Page;
use Fortuna\Model\Produto;
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

    $produtos = Lazer::table(Produto::$tabela_db)
        ->findAll()
        ->asArray();
    
    foreach ($produtos as &$produto) {
        if($produto['valor']) {
            $produto['valor'] = number_format($produto['valor'], 2, ',', '.');
        }
    }

    $page = new Page([
        'header' => false,
        'data' => [
            'site_titulo' => 'Produtos',
            'res_path'    => $resource_path
        ]
    ], '/views/admin/');

    $page->setTpl("admin_produtos", [
        'produtos' => $produtos?: []
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

$app->post('/admin/produto/cadastro', function (Request $request, Response $response, $args) {
    #UsuarioAdmin::checkLogin();

    try {
        $dados = json_decode($request->getBody()->getContents(), true);

        $produto = new Produto($dados);

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
