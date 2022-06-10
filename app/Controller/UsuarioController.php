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
    public static function getRoutes(App $app)
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

        $app->post('/usuario/login', function () {
            Usuario::login(
                $_POST['email'],
                $_POST['senha']
            );
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
