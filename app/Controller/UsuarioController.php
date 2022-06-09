<?php

use Fortuna\Mysql;
use Fortuna\Model\Usuario;
use Doctrine\DBAL\Query\QueryBuilder;

$app->post('/usuario/login', Usuario::login(
    $_POST['email'],
    $_POST['senha']
));

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
