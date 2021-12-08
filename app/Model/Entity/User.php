<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * Identificador do usuário
     *
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     *
     * @var string
     */
    public $nome;

    /**
     * Login
     *
     * @var string
     */
    public $login;

    /**
     * Senha do usuário
     *
     * @var string
     */
    public $senha;

    /**
     * Retorna objeto com os valores do banco de dados
     *
     * @param string $login
     * @return User
     */
    public static function getUserByLogin($login)
    {
        return self::getUsers("login = '$login'")->fetchObject(self::class);
    }

    /**
     * Retorna objeto com os valores do banco de dados
     *
     * @param integer $id
     * @return User
     */
    public static function getUserById($id)
    {
        return self::getUsers("id = $id")->fetchObject(self::class);
    }

    /**
     * Retorna usuários do BD
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuario'))->select($where, $order, $limit, $fields);
    }

    /**
     * Realiza a inserção de novos dados na POSTAGEM
     *
     * @return boolean
     */
    public function cadastrar()
    {
        $this->id = (new Database('usuario'))->insert([
            'login' => $this->login,
            'nome' => $this->nome,
            'senha' => $this->senha
        ]);

        return true;
    }

    /**
     * Atualiza um registro de USUARIO
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('usuario'))->update("id = $this->id", [
            'login' => $this->login,
            'nome' => $this->nome,
            'senha' => $this->senha,
        ]);
    }

    /**
     * Apaga um registo de USUARIO
     *
     * @return boolean
     */
    public function excluir()
    {
        return (new Database('usuario'))->delete("id = $this->id");
    }
}
