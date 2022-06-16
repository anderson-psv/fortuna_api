<?php

use Fortuna\Page;
use Fortuna\Model\Consumidor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$res_path = './../../res/';

$app->get('/consumidor/login', function (Request $request, Response $response, $args) use ($res_path) {

    if (Consumidor::checkLogin()) {
        header('Location: /');
        exit;
    }

    $page = new Page([
        'data' => [
            'site_titulo' => 'Login',
            'res_path'    => $res_path
        ]
    ]);

    $page->setTpl("login");

    return $response->withStatus(200);
});

$app->post('/consumidor/login', function (Request $request, Response $response, $args) use ($res_path) {
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

$app->get('/consumidor/cadastro', function (Request $request, Response $response, $args) use ($res_path) {
    $params = $request->getQueryParams();
    $body   = $request->getBody()->getContents();
    $page = new Page([
        'data' => [
            'site_titulo' => 'Cadastro',
            'res_path'    => $res_path
        ]
    ]);

    $page->setTpl("consumidor_cadastro");

    return $response->withStatus(200);
});

$app->post('/consumidor/cadastro', function (Request $request, Response $response, $args) {
    $body = json_decode($request->getBody()->getContents(), true);

    try {
        $consumidor = new Consumidor($body);
        if (!$consumidor->save()) {
            throw new Exception("Não foi possível realizar o cadastro!", 7400);
        }

        $response->getBody()->write(json_encode([
            'status'  => 'success',
            'message' => 'Cadastro realizado com sucesso!'
        ]));

        //Cria a session com login
        Consumidor::login($body['email'], $body['senha']);
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }

    return $response->withStatus(200);
});

$app->get('/consumidor/alterar', function (Request $request, Response $response, $args) use ($res_path) {
    Consumidor::checkLogin(true);

    $page = new Page([
        'data' => [
            'site_titulo' => 'Alterar',
            'res_path'    => $res_path
        ]
    ]);

    $page->setTpl("consumidor_alterar", $_SESSION[Consumidor::SESSION]);

    return $response->withStatus(200);
});

$app->post('/consumidor/alterar', function (Request $request, Response $response, $args) {
    Consumidor::checkLogin(true);

    try {
        $body = json_decode($request->getBody()->getContents(), true);

        $body = array_merge($body, [
            'id' => $_SESSION[Consumidor::SESSION]['id'] //Add o id para identificação da alteração
        ]);

        $consumidor = new Consumidor($body);
        if (!$consumidor->save()) {
            throw new Exception("Não foi possível realizar a alteração!", 7400);
        }

        $response->getBody()->write(json_encode([
            'status'  => 'success',
            'message' => 'Alteração realizada com sucesso!'
        ]));
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }

    return $response->withStatus(200);
});
