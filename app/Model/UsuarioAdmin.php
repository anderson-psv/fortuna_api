<?php

namespace Fortuna\Model;

use Exception;
use Fortuna\Model;
use Lazer\Classes\Database as Lazer;

class UsuarioAdmin extends Model
{
    const SESSION        = "UsuarioAdmin";
    const SECRET         = "FortunaPhp7.4_Secret";
    const SECRET2        = "FortunaPhp7.4_SecondSecret";
    const ERROR          = "usuarioError";
    const ERROR_REGISTER = "usuarioErrorRegister";
    const SUCCESS        = "usuarioSuccess";

    public static string $tabela_db = 'f_usuario_admin';
    public static array $campos_db  = [
        'id',
        'email',
        'senha'
    ];

    public function setIdusuario(string $idusuario)
    {
        $this->idusuario = $idusuario;
        return $this;
    }

    public function getIdusuario()
    {
        return $this->idusuario;
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

    function __construct(array $dados = [])
    {
        if ($dados) {
            $this->setDados($dados);
        }
    }

    public function validarDados()
    {
        if (empty($this->email)) {
            throw new Exception("Informe o e-mail", 7400);
        }

        if (empty($this->senha)) {
            throw new Exception("Informe a senha", 7400);
        }
    }

    public function getUsuarioDb(string $idusuario)
    {
        try {
            $db_user = Lazer::table(self::$tabela_db)
                ->where('idusuario', '=', $idusuario)
                ->find()
                ->asArray();

            if (!$db_user) {
                throw new Exception("Usuário não encontrado", 7400);
            }

            $this->setDados($db_user, false);

            return $this;
        } catch (\Throwable $th) {
            throw new Exception("Erro ao buscar usuário no banco de dados", 7400);
        }
    }

    public function save()
    {
        $this->validarDados();

        $table = Lazer::table(self::$tabela_db);
        $table->set($this->getDados());

        $is_insert = !!$this->id;

        try {
            if ($is_insert) {
                if ($table->insert()) {
                    return $table->getField('id');
                }
                throw new Exception("Erro ao inserir usuário", 7400);
            }
            //Update
            if ($table->save()) {
                return $this->id;
            }

            throw new Exception("Erro ao atualizar usuário", 7400);
        } catch (\Throwable $th) {
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
        } catch (\Throwable $th) {
            throw new Exception("Erro ao deletar usuário", 7400);
        }
    }

    public static function getFromSession()
    {
        $usuario = new self();

        if (isset($_SESSION[self::SESSION]) && (int)$_SESSION[self::SESSION]['idusuario'] > 0) {
            $usuario->setDados($_SESSION[self::SESSION]);
        }

        return $usuario;
    }

    public static function checkLogin($inadmin = true)
    {
        if (
            !isset($_SESSION[self::SESSION])
            ||
            !$_SESSION[self::SESSION]
            ||
            !(int)$_SESSION[self::SESSION]['idusuario'] > 0
        ) {
            return false;
        }

        return false;
    }

    public static function login($email, $password)
    {
        $db_usuario = Lazer::table(self::$tabela_db)
            ->where('email', '=', $email)
            ->find()
            ->asArray();

        if (!$db_usuario) {
            throw new \Exception("Usuário inexistente ou senha inválida.", 7400);
        }

        if (password_verify($password, $db_usuario["senha"]) === true) {
            $usuario = new self();

            $usuario->setDados($db_usuario);

            //Remove o hash para nao salvar na sessão
            unset($db_usuario['senha']);

            $_SESSION[self::SESSION] = $usuario->getDados();

            return $usuario;
        } else {
            throw new \Exception("Usuário inexistente ou senha inválida.", 7400);
        }
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
    /* Sucesso Fim*/

    public static function getPasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 14
        ]);
    }
}
