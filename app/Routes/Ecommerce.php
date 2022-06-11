<?php

use Fortuna\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function (Request $request, Response $response, $args) {
	#$params = $request->getQueryParams();
	#$body   = $request->getBody()->getContents();
	$page = new Page([
		'data' => [
			'site_titulo' => 'Home'
		]
	]);

	$page->setTpl("index");

	return $response->withStatus(200);
});

$app->get('/sobre', function (Request $request, Response $response, $args) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Sobre'
		]
	]);

	$page->setTpl("sobre");

	return $response->withStatus(200);
});

$app->get('/produtos', function (Request $request, Response $response, $args) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Produtos'
		]
	]);

	$page->setTpl("produtos");

	return $response->withStatus(200);
});

$app->get('/contato', function (Request $request, Response $response, $args) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Contato'
		]
	]);

	$page->setTpl("contato");

	return $response->withStatus(200);
});

$app->get('/login', function (Request $request, Response $response, $args) {
	$page = new Page([
		'data' => [
			'site_titulo' => 'Login'
		]
	]);

	$page->setTpl("login");

	return $response->withStatus(200);
});
