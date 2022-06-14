<?php

use Fortuna\Page;
use Fortuna\Model\Consumidor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/consumidor/login', function (Request $request, Response $response, $args) use ($app) {

    if (Consumidor::checkLogin()) {
        header('Location: /');
        exit;
    }
    
    $page = new Page([
        'data' => [
            'site_titulo' => 'Login'
        ]
    ]);

    $page->setTpl("login");

    return $response->withStatus(200);
});

$app->post('/consumidor/login', function (Request $request, Response $response, $args) {
    $params = $request->getQueryParams();
    $body   = json_decode($request->getBody()->getContents(), true);

    try {
        $usuario = Consumidor::login($body['email'], $body['senha']);
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }

    if ($usuario) {
        $response->getBody()->write(json_encode([
            'usuario' => $usuario,
            'status'  => 'success',
            'message' => 'Login realizado com sucesso!'
        ]));

        return $response->withStatus(200);
    }

    return $response->withStatus(200);
});

$app->get('/consumidor/logout', function (Request $request, Response $response, $args) {
    Consumidor::logout();
    header('Location: /');
    exit;
});

$app->get('/consumidor/cadastro', function (Request $request, Response $response, $args) {
    $params = $request->getQueryParams();
    $body   = $request->getBody()->getContents();
    $page = new Page([
        'data' => [
            'site_titulo' => 'Cadastro'
        ]
    ]);

    $page->setTpl("consumidor_cadastro");

    return $response->withStatus(200);
});
