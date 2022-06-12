<?php

namespace Fortuna;

class Model
{
    public static string $tabela_db = '';
    public static array $campos_db  = [];

    public function setDados(array $dados, bool $validar = true)
    {
        foreach (self::$campos_db as $campo => $dado) {
            $this->$campo = $dado;
        }

        if ($validar) {
            $this->validarDados($dados);
        }

        return $this;
    }

    public function getDados()
    {
        $dados = [];

        foreach (self::$campos_db as $campo) {
            $dados[$campo] = $this->$campo;
        }

        return $dados;
    }

    public function validarDados()
    {
    }
}
