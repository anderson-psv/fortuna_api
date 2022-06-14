<?php

namespace Fortuna;

interface Model
{
    public function setDados(array $dados, bool $validar = true);
    public function getDados();
    public function validarDados();
    public function save();
    public function delete(string $id);
}
