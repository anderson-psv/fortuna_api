<?php

namespace Fortuna;

interface iModel
{
    public function setDados(array $dados, bool $validar = true);
    public function getDados();
    public function validarDados();
    public function save();
    public function delete(string $id);
}
