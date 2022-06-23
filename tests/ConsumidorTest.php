<?php

namespace Tests\Feature;

use Fortuna\Model\Consumidor;
use PHPUnit\Framework\TestCase;

final class ConsumidorTest extends TestCase
{
    public function testConsumidor()
    {
        $consumidor = new Consumidor();
        $this->assertInstanceOf(Consumidor::class, $consumidor);
    }

    public function  testSetDadosConsumidor()
    {
        define('ID_HASH', '123456789');

        $array = [
            'nome'   => 'Some random dude',
            'email'  => 'email@email.com.br',
            'senha'  => '123456789',
            'status' => 'INATIVO'
        ];

        $consumidor = (new Consumidor())
            ->setIgnorarSenha()
            ->setDados($array);

        $array['id'] = -1;
        unset($array['senha']);

        $this->assertEquals($array, $consumidor->getDados());
    }
}
