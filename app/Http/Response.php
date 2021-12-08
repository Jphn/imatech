<?php

namespace App\Http;

class Response
{
    /**
     * Código do Status HTTP
     *
     * @var integer
     */
    private $httpCode = 200;

    /**
     * Cabeçalhos do Response
     *
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo retornado
     *
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do Response
     *
     * @var mixed
     */
    private $content;

    /**
     * Método responsável por iniciar a classe e definir os valores
     *
     * @param integer $httpCode
     * @param mixed $content
     */
    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }

    /**
     * Método responsável por alterar o contet type do response
     *
     * @param mixed $contentType
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    /**
     * Adicionar um registro no cabeçalho de Response
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Responsável por enviar os Headers para o navegador
     */
    private function sendHeaders()
    {
        // Status
        http_response_code($this->httpCode);

        // Enviar Headers
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    /**
     * Responsável por enviar a resposta para o usuário
     */
    public function sendResponse()
    {
        // Enviar os Headers
        $this->sendHeaders();

        // Imprime o conteúdo
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;
                break;

            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
                break;
        }
    }
}
