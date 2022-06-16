<?php

use Fortuna\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$res_path = './../';

$app->get('/', function (Request $request, Response $response, $args) use ($res_path) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Home',
			'res_path'    => $res_path
		]
	]);

	$page->setTpl("index");

	return $response->withStatus(200);
});



$app->get('/sobre', function (Request $request, Response $response, $args) use ($res_path) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Sobre',
			'res_path'    => $res_path
		]
	]);

	$page->setTpl("sobre");

	return $response->withStatus(200);
});

$app->get('/produtos', function (Request $request, Response $response, $args) use ($res_path) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Produtos',
			'res_path'    => $res_path
		]
	]);

	$page->setTpl("produtos");

	return $response->withStatus(200);
});

$app->get('/contato', function (Request $request, Response $response, $args) use ($res_path) {
	$page = new Page([
		'data' => [
			'site_titulo'    => 'Contato',
			'res_path'    => $res_path
		]
	]);

	$page->setTpl("contato");

	return $response->withStatus(200);
});
