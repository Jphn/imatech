<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Post
{
    /**
     * Identificador da postagem
     *
     * @var integer
     */
    public $id;

    /**
     * Data em que a postagem foi feita
     *
     * @var string
     */
    public $data;

    /**
     * Titulo da postagem
     *
     * @var string
     */
    public $titulo;

    /**
     * Conteúdo da postagem
     *
     * @var string
     */
    public $conteudo;

    /**
     * Identificador do usuário responsável pela criação da postagem
     *
     * @var integer
     */
    public $idUsuario;

    /**
     * Identificador da categoria da postagem
     *
     * @var integer
     */
    public $idCategoria;

    /**
     * Estado da postagem (Disponível ou não)
     *
     * @var boolean
     */
    public $available;

    /**
     * Retorna as postagens no bando de dados
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getPosts($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('postagem'))->select($where, $order, $limit, $fields);
    }

    /**
     * Realiza a inserção de novos dados na POSTAGEM
     *
     * @return boolean
     */
    public function cadastrar()
    {
        // Atribui a data
        $this->data = date("Y-m-d H:i:s");

        // Realiza a inserção, retornando o ID
        $this->id = (new Database('postagem'))->insert([
            'titulo' => $this->titulo,
            'conteudo' => $this->conteudo,
            'data' => $this->data,
            'idUsuario' => $this->idUsuario,
            'idCategoria' => $this->idCategoria,
            'available' => $this->available ? 1 : 0
        ]);

        // Retorna o "sucesso"
        return true;
    }

    /**
     * Retorna objeto com os valores do banco de dados
     *
     * @param integer $id
     * @return Post
     */
    public static function getPostById($id)
    {
        return self::getPosts("id = $id")->fetchObject(self::class);
    }

    /**
     * Realiza a atualização de novos dados na POSTAGEM
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('postagem'))->update("id = $this->id", [
            'titulo' => $this->titulo,
            'conteudo' => $this->conteudo,
            'idCategoria' => (int)$this->idCategoria,
            'available' => (bool)$this->available ? 1 : 0
        ]);
    }

    /**
     * Apaga um registro do banco de dados
     *
     * @return boolean
     */
    public function excluir()
    {
        return (new Database('postagem'))->delete("id = $this->id");
    }
}
