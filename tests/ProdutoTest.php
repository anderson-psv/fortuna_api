<?php

namespace Tests\Feature;

use Fortuna\Model\Produto;
use PHPUnit\Framework\TestCase;

final class ProdutoTest extends TestCase
{
    public function testProduto()
    {
        $produto = new Produto();
        $this->assertInstanceOf(Produto::class, $produto);
    }

    public function setUp(): void
    {
        if(!defined('BASE_DIR')) {
            define('BASE_DIR', dirname(__DIR__));
        }
        if(!defined('LAZER_DATA_PATH')) {
            define('LAZER_DATA_PATH', BASE_DIR . '/database/');
        }
    }

    public function testSetDadosProduto()
    {
        $img_path = __DIR__ . '/img/img_teste.jpg';

        $type = pathinfo($img_path, PATHINFO_EXTENSION);
        $data = file_get_contents($img_path);
        $imagem = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $array = [
            'descricao' => 'Some random description',
            'valor'     => 123.45,
            'status'    => 'ATIVO',
            'imagem'    => $imagem
        ];

        $produto = new Produto($array);

        $array['id'] = -1;
        $array['imagem'] = true;

        $dados = $produto->getDados();
        $dados['imagem'] = is_file($dados['imagem']);

        $this->assertNotFalse($data);
        $this->assertEquals($array, $dados);
    }
}
