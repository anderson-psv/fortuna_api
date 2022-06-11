<?php

use Fortuna\Page;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/', function(Request $request, Response $response, $args) {
	$params = $request->getQueryParams();
	$body   = $request->getBody()->getContents();

	$page = new Page();
	$page->setTpl("index", [
	    "teste" => [
            'chave1' =>'um',
			'chave2' =>'dois',
			'chave3' =>'tres'
        ]
	]);

    return $response->withStatus(200);
});