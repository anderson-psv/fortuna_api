<?php

namespace Fortuna\Model;

use Exception;
use Fortuna\iModel;
use Fortuna\Functions;
use Lazer\Classes\Database as Lazer;

class Produto implements iModel
{
    public static string $tabela_db = 'f_produto';
    public static array $campos_db  = [
        'id',
        'descricao',
        'valor',
        'status',
        'imagem'
    ];

    private int $id = -1;

    private string $descricao = '';
    private float $valor      = -1;
    private string $status    = '';

    function __construct(array $dados = [])
    {
        if ($dados) {
            $this->setDados($dados);
        }
    }

    public function setDados(array $dados, bool $validar = true)
    {
        foreach (self::$campos_db as $campo) {
            if (isset($dados[$campo])) {
                $this->$campo = Functions::modelCasting(gettype($this->$campo), $dados[$campo]);
            }
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

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setImagem($imagem)
    {
        $this->imagem = $imagem;
        return $this;
    }

    public function getImagem()
    {
        return $this->imagem;
    }

    public function validarDados()
    {
        try {
            if (empty($this->descricao)) {
                throw new Exception('O campo descrição é obrigatório', 7400);
            }

            if ($this->valor <= 0) {
                throw new Exception('Informe um valor maior que zero', 7400);
            }

            if (!in_array($this->status, ['ATIVO', 'INATIVO'])) {
                throw new Exception('Status inválido', 7400);
            }

            if (strpos($this->imagem, 'base64') !== false) {
                if (strpos($this->imagem, 'data:image/') === false) {
                    throw new Exception('Formato de imagem inválido', 7400);
                }

                $ext    = explode('/', mime_content_type($this->imagem))[1];
                $base64 = explode(',', $this->imagem)[1];
                if (strlen($base64) > 1000000) {
                    throw new Exception('Tamanho da imagem excede o limite', 7400);
                }

                $img_dir = BASE_DIR . '/database/img/';
                if (!is_dir($img_dir)) {
                    mkdir($img_dir);
                }

                if ($this->id < 0) {
                    $tmp_id = Lazer::table(self::$tabela_db)->lastId() + 1;
                } else {
                    $tmp_id = $this->id;
                }

                $file_path = $img_dir . 'produto_img_' . $tmp_id . '.' . $ext;
                if (!file_put_contents($file_path, base64_decode($base64))) {
                    throw new Exception('Erro ao salvar imagem!', 7400);
                };

                $this->imagem = $file_path;
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getProdutoDb(int $idproduto)
    {
        try {
            $db_produto = Lazer::table(self::$tabela_db)
                ->where('id', '=', $idproduto)
                ->limit(1)
                ->findAll()
                ->asArray();

            $db_produto = $db_produto[0] ?: [];

            if (!$db_produto) {
                throw new Exception("Produto não encontrado", 7400);
            }

            $this->setDados($db_produto, false);

            return $this;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw new Exception("Erro ao buscar produto no banco de dados", 7400);
        }
    }

    public function save()
    {
        $this->validarDados();

        $is_insert = ($this->id < 0);
        $table     = Lazer::table(self::$tabela_db);
        try {
            if ($is_insert) {
                $table->set($this->getDados());
                $table->insert();
                if ($id = $table->getField('id')) {
                    return $id;
                }

                throw new Exception("Erro ao inserir produto", 7400);
            }

            //Update
            $table->find($this->id)
                ->set($this->getDados());

            $table->save();

            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            $msg = "Erro ao atualizar dados do produto";

            if ($is_insert) {
                $msg = "Erro ao salvar dados do produto";
            }
            throw new Exception($msg, 7400);
        }
    }

    public function delete(string $idproduto)
    {
        try {
            $row = Lazer::table(self::$tabela_db)
                ->where('id', '=', $idproduto);

            if (!$row->delete()) {
                throw new Exception("Não foi possivel deletar o produto!", 7400);
            }
            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw new Exception("Erro ao deletar produto", 7400);
        }

        return false;
    }
}
