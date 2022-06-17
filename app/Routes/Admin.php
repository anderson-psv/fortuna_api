<?php

use Fortuna\PageAdmin;
use Fortuna\Model\Produto;
use Fortuna\Model\UsuarioAdmin;
use Lazer\Classes\Database as Lazer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/admin', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    $page = new PageAdmin([
        'data' => [
            'site_titulo' => 'Home'
        ]
    ]);

    $page->setTpl("index");

    return $response->withStatus(200);
});

$app->get('/admin/login', function (Request $request, Response $response, $args) {

    $page = new PageAdmin([
        'header' => false,
        'footer' => false,
        'data' => [
            'site_titulo' => 'Login'
        ]
    ]);

    $page->setTpl("login");

    return $response->withStatus(200);
});

$app->post('/admin/login', function (Request $request, Response $response, $args) {

    try {
        $dados = json_decode($request->getBody()->getContents(), true);

        $usuario = UsuarioAdmin::login($dados['email'], $dados['senha']);

        if (!$usuario) {
            throw new Exception('Usuário ou senha inválidos', 7400);
        }

        $response->getBody()->write(json_encode([
            'status' => 'success'
        ]));
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $th->getMessage()
        ]));
    }

    return $response->withStatus(200);
});

$app->get('/admin/logout', function (Request $request, Response $response, $args) {
    UsuarioAdmin::logout();
    header('Location: /admin/login');
    exit;
});

$app->get('/admin/produtos', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin();

    $produtos = Lazer::table(Produto::$tabela_db)
        ->findAll()
        ->asArray();

    foreach ($produtos as &$produto) {
        if ($produto['valor']) {
            $produto['valor'] = number_format($produto['valor'], 2, ',', '.');
        }
    }

    $page = new PageAdmin([
        'header' => false,
        'data' => [
            'site_titulo' => 'Produtos'
        ]
    ]);

    $page->setTpl("admin_produtos", [
        'produtos' => $produtos ?: []
    ]);

    return $response->withStatus(200);
});

$app->get('/admin/produtos/cadastro', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    $page = new PageAdmin([
        'header' => false,
        'sub_res' => true,
        'data' => [
            'site_titulo' => 'Cadastrar Produto'
        ],
    ]);

    $page->setTpl("admin_produto_cadastro", []);

    return $response->withStatus(200);
});

$app->post('/admin/produto/cadastro', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

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

$app->get('/admin/produtos/alterar/{idproduto}', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');
    $idproduto = (int) $request->getAttribute('idproduto');

    if (empty($idproduto)) {
        header('Location: /admin/produtos');
        exit;
    }

    $produto = (new Produto())
        ->getProdutoDb($idproduto)
        ->getDados();

    $page = new PageAdmin([
        'header'  => false,
        'sub_res' => true,
        'data' => [
            'site_titulo' => 'Alterar Produto'
        ]
    ]);

    $page->setTpl("admin_produto_editar", $produto);

    return $response->withStatus(200);
});

$app->post('/admin/produto/alterar/{idproduto}', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    try {
        $idproduto = $request->getAttribute('idproduto');

        if (empty($idproduto)) {
            throw new Exception('ID produto inváldo', 7400);
        }

        $dados = json_decode($request->getBody()->getContents(), true);

        $produto = (new Produto())
            ->getProdutoDb($idproduto)
            ->setDados($dados);

        if (!$produto->save()) {
            throw new Exception('Erro ao alterar produto', 7400);
        }

        $response->getBody()->write(json_encode([
            'status'  => 'success',
            'message' => 'Produto alterado com sucesso!'
        ]));
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }

    return $response->withStatus(200);
});

$app->post('/admin/produto/remover/{idproduto}', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    try {
        $idproduto = $request->getAttribute('idproduto');
        $produto = (new Produto());
        if (!$produto->delete($idproduto)) {
            throw new Exception('Erro ao remover produto', 7400);
        }

        $response->getBody()->write(json_encode([
            'status'  => 'success',
            'message' => 'Produto removido com sucesso!'
        ]));
    } catch (\Throwable $th) {
        $response->getBody()->write(json_encode([
            'status'  => 'error',
            'message' => $th->getMessage()
        ]));
    }

    return $response->withStatus(200);
});

$app->get('/admin/usuarios', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    $usuarios = Lazer::table(UsuarioAdmin::$tabela_db)
        ->findAll()
        ->asArray();

    foreach ($usuarios as &$usuario) {
        unset($usuario['senha']);
    }

    $page = new PageAdmin([
        'header'  => false,
        'sub_res' => true,
        'data' => [
            'site_titulo' => 'Usuarios'
        ]
    ]);

    var_dump($usuarios);
    $page->setTpl("admin_usuarios", [
        'usuarios' => $usuarios
    ]);

    return $response->withStatus(200);
});

$app->get('/admin/usuarios/cadastro', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    $page = new PageAdmin([
        'header'  => false,
        'sub_res' => true,
        'data' => [
            'site_titulo' => 'Usuarios'
        ]
    ]);

    $page->setTpl("admin_usuario_cadastro");

    return $response->withStatus(200);
});

$app->post('/admin/usuario/cadastro', function (Request $request, Response $response, $args) {
    UsuarioAdmin::checkLogin('/admin/login');

    try {
        $dados   = json_decode($request->getBody()->getContents(), true);
        $usuario = new UsuarioAdmin($dados);

        if (!$usuario->save()) {
            throw new \Exception('Erro ao salvar usuario!', 7400);
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
