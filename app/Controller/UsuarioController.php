<?php

namespace Fortuna\Controller;

use Slim\App;
use Exception;
use Fortuna\Mysql;
use Fortuna\Model\Usuario;
use Doctrine\DBAL\Query\QueryBuilder;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    public static function register(App $app)
    {
        $app->post('/teste', function (Request $request, Response $response, $args) {
            #$request->getBody()->getContents();
            $response->getBody()->write(json_encode([
                'uno',
                'dos',
                'tres'
            ], JSON_PRETTY_PRINT));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        });

        $app->post('/usuario/login', function (Request $request, Response $response, $args) {
            $dados = json_decode($request->getBody()->getContents(), true);

            try {
                $usuario = Usuario::login(
                    $dados['email'],
                    $dados['senha']
                );
            } catch (\Throwable $th) {
                $response->getBody()->write(json_encode([
                    'error' => $th->getMessage()
                ]));

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
           

            if($usuario) {
                $response->getBody()->write(json_encode($usuario));
                return $response->withStatus(200);
            }

            $response->getBody()->write(json_encode([
                'message' => 'Usuário ou senha inválidos!'
            ]));

            return $response
                ->withStatus(404);
        });

        $app->post('/usuario/listar', function () {
            $isadmin = Usuario::checkLogin();

            if (!$isadmin) {
                throw new Exception("Não autorizado", 7400);
            }

            $qb = new QueryBuilder((new Mysql())->getDb());

            return $qb->select('idusuario', 'email', 'isadmin')
                ->from(Usuario::$tabela_db)
                ->fetchAllAssociative();
        });

        $app->post('/usuario/cadastro', function () {
            $usuario = new Usuario();
            $usuario->setDados($_POST);

            if ($usuario->save()) {
                return 'Usuário cadastrado com sucesso!';
            }
        });

        $app->post('/usuario/alterar', function () {
            if (empty($_POST['idusuario'])) {
                throw new Exception("Informe o idusuario!!", 7400);
            }

            $usuario = new Usuario();
            $usuario->setDados($_POST);

            if ($usuario->save()) {
                return 'Usuário cadastrado com sucesso!';
            }
        });

        $app->post('/usuario/deletar', function () {
            if (empty($_POST['idusuario'])) {
                throw new Exception("Informe o idusuario!!", 7400);
            }

            $usuario = (new Usuario())
                ->setIdusuario($_POST['idusuario']);

            if ($usuario->save()) {
                return 'Usuário cadastrado com sucesso!';
            }
        });

        $app->post('/usuario/getUsuario/:idusuario', function ($idusuario) {
            $isadmin = Usuario::checkLogin();

            if ($_SESSION[Usuario::SESSION]['idusuario'] !== $idusuario && !$isadmin) {
                throw new Exception("Você não tem permissão para acessar este usuário", 7400);
                return;
            }

            $qb = new QueryBuilder((new Mysql())->getDb());
            return $qb->select('idusuario', 'email', 'isadmin')
                ->from(Usuario::$tabela_db)
                ->fetchAssociative();
        });
    }
}
