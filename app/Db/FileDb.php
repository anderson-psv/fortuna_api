<?php

namespace Fortuna\Db;

use Exception;
use Fortuna\Logger;
use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers\Validate;

class FileDb
{
    public static function createAllTables(string $file_path)
    {
        $json = json_decode(file_get_contents($file_path), true);

        if (!$json) {
            throw new Exception("Arquivo inicial do Banco de Dados não encontrado!", 7400);
        }

        if (!is_dir(LAZER_DATA_PATH)) {
            mkdir(LAZER_DATA_PATH);
        }

        foreach ($json as $tabela => $campos) {
            try {
                Validate::table($tabela)->exists();
            } catch (\Throwable $th) {
                //Tabela não existe, cria ela
                try {
                    Lazer::create($tabela, $campos);
                } catch (\Throwable $th) {
                    //Ocorreu erro gera log
                    (new Logger)->error($th->getMessage(), [
                        `tabela` => $tabela,
                        `campos` => $campos,
                    ]);
                }
            }
        }
    }
}
