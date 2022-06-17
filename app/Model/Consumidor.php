<?php

namespace Fortuna\Model;

use Exception;
use Fortuna\iModel;
use Fortuna\Functions;
use Lazer\Classes\Database as Lazer;

class Consumidor implements iModel
{
    public static string $tabela_db = 'f_consumidor';
    public static array $campos_db  = [
        'id',
        'nome',
        'email',
        'senha',
        'status'
    ];

    const SESSION        = "Consumidor";
    const SECRET         = "FortunaPhp7.4_Secret";
    const SECRET2        = "FortunaPhp7.4_SecondSecret";
    const ERROR          = "ConsumidorError";
    const ERROR_REGISTER = "ConsumidorErrorRegister";
    const SUCCESS        = "ConsumidorSuccess";

    private int $id = -1;

    private string $nome  = '';
    private string $email = '';
    private string $senha = '';
    private string $status = '';

    private bool $ignorar_senha = false;

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

        if ($this->ignorar_senha) {
            unset($dados['senha']);
        }

        return $dados;
    }

    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setSenha(string $senha)
    {
        $this->senha = $senha;
        return $this;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function validarDados()
    {
        try {
            if (empty($this->nome)) {
                throw new Exception("Informe o nome", 7400);
            }

            if (empty($this->email)) {
                throw new Exception("Informe o e-mail", 7400);
            }

            if (!$this->ignorar_senha) {
                if (empty($this->senha)) {
                    throw new Exception("Informe a senha", 7400);
                }

                if (!Functions::isHash($this->senha)) {
                    $this->senha = Functions::getPasswordHash($this->senha);
                }
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw $th;
        }
    }

    public function getUsuarioDb(string $idusuario)
    {
        try {
            $db_user = Lazer::table(self::$tabela_db)
                ->where('id', '=', $idusuario)
                ->findAll()
                ->asArray();

            $db_user = $db_user[0] ?: [];
            if (!$db_user) {
                throw new Exception("Usuário não encontrado", 7400);
            }

            $this->setDados($db_user, false);

            return $this;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw new Exception("Erro ao buscar usuário no banco de dados", 7400);
        }
    }

    public function save()
    {
        $this->validarDados();
        $this->verificarEmailUnico();

        $is_insert = ($this->id < 0);
        $table     = Lazer::table(self::$tabela_db);
        try {
            if ($is_insert) {
                if ($this->ignorar_senha) {
                    throw new Exception("Obrigatório senha no cadastro!", 7400);
                }
                $table->set($this->getDados());
                $table->insert();
                if ($id = $table->getField('id')) {
                    return $id;
                }

                throw new Exception("Erro ao inserir usuário", 7400);
            }

            //Update
            $table->find($this->id)
                ->set($this->getDados());

            $table->save();

            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            $msg = "Erro ao atualizar dados do usuário";

            if ($is_insert) {
                $msg = "Erro ao salvar dados do usuário";
            }
            throw new Exception($msg, 7400);
        }
    }

    public function delete(string $idconsumidor)
    {
        try {
            $row = Lazer::table(self::$tabela_db)
                ->where('id', '=', $idconsumidor);

            if (!$row->delete()) {
                throw new Exception("Não foi possivel deletar o usuário!", 7400);
            }

            return true;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            throw new Exception("Erro ao deletar usuário", 7400);
        }
    }

    public static function getFromSession(string $campo = null)
    {
        if (isset($_SESSION[self::SESSION]) && (int)$_SESSION[self::SESSION]['id'] > 0) {
            if ($campo) {
                return $_SESSION[self::SESSION][$campo] ?: null;
            }

            return $_SESSION[self::SESSION];
        }

        return [];
    }

    public static function checkLogin($redirect_home = false)
    {
        $retorno = true;
        if (
            !isset($_SESSION[self::SESSION])
            ||
            !$_SESSION[self::SESSION]
            ||
            !(int)$_SESSION[self::SESSION]['id'] > 0
        ) {
            $retorno = false;
        }

        if ($redirect_home) {
            header('Location: /');
            exit;
        }

        return $retorno;
    }

    public static function login($email, $password)
    {
        if (empty($email)) {
            throw new Exception("Informe o e-mail", 7400);
        }

        if (empty($password)) {
            throw new Exception("Informe a senha", 7400);
        }

        $table      = Lazer::table(self::$tabela_db);
        $db_usuario = $table
            ->where('email', '=', $email)
            ->andWhere('status', '=', 'ATIVO')
            ->find()
            ->asArray();

        $db_usuario = $db_usuario[0] ?: null;

        if (!$db_usuario) {
            throw new \Exception("Usuário inexistente ou senha inválida.", 7400);
        }

        if (password_verify($password, $db_usuario["senha"]) == true) {
            //Remove o hash para nao salvar na sessão
            unset($db_usuario['senha']);

            $_SESSION[self::SESSION] = $db_usuario;

            return $db_usuario;
        }

        throw new \Exception("Usuário inexistente ou senha inválida.", 7400);
    }

    public static function verifyLogin($inadmin = true)
    {
        if (!self::checkLogin($inadmin)) {
            if ($inadmin) {
                header("Location: /admin/login");
            } else {
                header("Location: /login");
            }
            exit;
        }
    }

    public static function logout()
    {
        $_SESSION[self::SESSION] = NULL;
    }

    /* Erros  */
    public static function setError($msg)
    {
        $_SESSION[self::ERROR] = $msg;
    }

    public static function getError()
    {
        $msg = (isset($_SESSION[self::ERROR])) ? $_SESSION[self::ERROR] : '';

        self::clearError();

        return $msg;
    }

    public static function clearError()
    {
        $_SESSION[self::ERROR] = NULL;
    }

    /* Erros de registro/cadastro */
    public static function setErrorRegister($msg)
    {

        $_SESSION[self::ERROR_REGISTER] = $msg;
    }

    public static function getErrorRegister()
    {

        $msg = (isset($_SESSION[self::ERROR_REGISTER]) && $_SESSION[self::ERROR_REGISTER]) ? $_SESSION[self::ERROR_REGISTER] : '';

        self::clearErrorRegister();

        return $msg;
    }

    public static function clearErrorRegister()
    {

        $_SESSION[self::ERROR_REGISTER] = NULL;
    }

    /* Sucesso */
    public static function setSuccess($msg)
    {
        $_SESSION[self::SUCCESS] = $msg;
    }

    public static function getSuccess()
    {
        $msg = (isset($_SESSION[self::SUCCESS])) ? $_SESSION[self::SUCCESS] : '';

        self::clearSuccess();

        return $msg;
    }

    public static function clearSuccess()
    {
        $_SESSION[self::SUCCESS] = NULL;
    }

    public function verificarEmailUnico()
    {
        try {
            $table = Lazer::table(self::$tabela_db);

            if ($this->id) {
                $table->where('id', '!=', $this->id);
            }

            $ja_existe = $table
                ->andwhere('email', '=', $this->email)
                ->limit(1)
                ->findAll()
                ->asArray();
            if (!$ja_existe) {
                return true;
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }

        throw new Exception("Email já cadastrado!", 7400);
    }

    public function setIgnorarSenha()
    {
        $this->ignorar_senha = true;
        return $this;
    }
}
