<?php

use Fortuna\Model\Produto;
use Fortuna\PageEcommerce;
use Lazer\Classes\Database as Lazer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response, $args) {
	$page = new PageEcommerce([
		'data' => [
			'site_titulo' => 'Home'
		]
	]);

	$page->setTpl("index");

	return $response->withStatus(200);
});

$app->get('/sobre', function (Request $request, Response $response, $args) {
	$page = new PageEcommerce([
		'data' => [
			'site_titulo' => 'Sobre'
		]
	]);

	$page->setTpl("sobre");

	return $response->withStatus(200);
});

$app->get('/produtos', function (Request $request, Response $response, $args) {

	try {
		$produtos = Lazer::table(Produto::$tabela_db)
			->where('status', '=', 'ATIVO')
			->findAll()
			->asArray();

		foreach ($produtos as &$produto) {
			$produto['valor'] = number_format($produto['valor'], 2, ',', '.');

			$imagem = $produto['imagem'] ?: '';

            if ($imagem && is_file($imagem)) {
                $type = pathinfo($imagem, PATHINFO_EXTENSION);
                $data = file_get_contents($imagem);
                $produto['imagem'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
		}
	} catch (\Throwable $th) {
		error_log($th->getMessage());
	}

	$page = new PageEcommerce([
		'data' => [
			'site_titulo' => 'Produtos'
		]
	]);

	$page->setTpl("produtos", [
		'produtos' => $produtos
	]);

	return $response->withStatus(200);
});

$app->get('/contato', function (Request $request, Response $response, $args) {
	$page = new PageEcommerce([
		'data' => [
			'site_titulo'    => 'Contato'
		]
	]);

	$page->setTpl("contato");

	return $response->withStatus(200);
});
