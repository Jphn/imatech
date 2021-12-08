<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;
use App\Model\Entity\Post as EntityPost;

class Category
{
    /**
     * Identificador da Categoria
     *
     * @var integer
     */
    public $id;

    /**
     * Nome da categoria
     *
     * @var string
     */
    public $nome;

    /**
     * Define se a categoria é necessária
     *
     * @var boolean
     */
    public $necessary = false;

    /**
     * Retorna objeto com os valores do banco de dados
     *
     * @param integer $id
     * @param boolean $necessary
     * @return User
     */
    public static function getCategoryById($id, $necessary = false)
    {
        if ($necessary) {
            return self::getCategories("id = $id AND necessary = 0")->fetchObject(self::class);
        } else {
            return self::getCategories("id = $id")->fetchObject(self::class);
        }
    }

    /**
     * Retorna as postagens no bando de dados
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getCategories($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('categoria'))->select($where, $order, $limit, $fields);
    }

    /**
     * Realiza a contagem do número de postagens cadastradas numa categoria específica
     *
     * @return integer
     */
    public function contagem()
    {
        return EntityPost::getPosts("idCategoria = $this->id", null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
    }

    /**
     * Cadastra uma nova categoria
     *
     * @return boolean
     */
    public function cadastrar()
    {
        $this->id = (new Database('categoria'))->insert([
            'nome' => $this->nome
        ]);

        return true;
    }

    /**
     * Atualiza um registro de CATEGORIA
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('categoria'))->update("id = $this->id", [
            'nome' => $this->nome
        ]);
    }

    /**
     * Apaga um registo de CATEGORIA
     *
     * @return boolean
     */
    public function excluir()
    {
        if ((new Database('postagem'))->update("idCategoria = $this->id", [
            'idCategoria' => 1
        ])) return (new Database('categoria'))->delete("id = $this->id");
    }
}
