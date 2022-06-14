<?php

namespace Fortuna;

class Functions
{
    public static function modelCasting($type, $valor)
    {
        if ($type !== "NULL") {
            #Só realiza casting para variaveis definidas com tipagem
            settype($valor, $type);
        }

        return $valor;
    }

    public static function isHash(string $text)
    {
        if (strpos($text, ID_HASH) !== false) {
            return true;
        }
        return false;
    }

    public static function getPasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 14
        ]);
    }
}
