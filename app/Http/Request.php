<?php

namespace App\Http;

class Request
{
    /**
     * Recebe a classe Router
     *
     * @var Router
     */
    private $router;

    /**
     * Método Http da requisição
     *
     * @var string
     */
    private $httpMethod;

    /**
     * URI
     *
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL (Get)
     *
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas pelo Post
     *
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição
     *
     * @var array
     */
    private $headers = [];

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->postVars = $_POST ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
    }

    /**
     * Responsável por separar os Gets da URI
     *
     * @return string
     */
    private function setUri()
    {
        // URI completa (Com Gets)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        // Remove os Gets da URI
        $xUri = explode('?', $this->uri);
        $this->uri = $xUri[0];
    }

    /**
     * Retorna a instância de Router
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar o método URI da requisição
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Método responsável por retornar o método Headers da requisição
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Método responsável por retornar o método Get da requisição
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar o método Post da requisição
     *
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }
}
