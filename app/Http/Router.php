<?php

namespace App\Http;

use Closure;
use Exception;
use ReflectionFunction;
use App\Http\Middleware\Queue as MiddlewareQueue;

class Router
{
    /**
     * Url completa do projeto (raiz)
     *
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     *
     * @var string
     */
    private $prefix = '';

    /**
     * Índice de rotas
     *
     * @var array
     */
    private $routes = [];

    /**
     * Instancia de Resquest
     *
     * @var Request
     */
    private $request;

    /**
     * Guarda o tipo de conteúdo da página
     *
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Responsável por contruir a classe Router
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Responsável por alterar o content type
     *
     * @param string $contentType
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Define o prefixo das rotas
     */
    private function setPrefix()
    {
        // Informações da Url
        $parseUrl = parse_url($this->url);

        // Define o Prefixo
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Responsável por adicionar uma rota na classe
     *
     * @param string $method
     * @param string $route
     * @param array $params
     */
    private function addRoute($method, $route, $params = [])
    {
        // Validação dos parâmetros
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        // Middlewares da rota
        $params['middlewares'] = $params['middlewares'] ?? [];

        // Variáveis da rota
        $params['variables'] = [];

        // Padrão de validação das variáveis das rotas
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        // Remove barra no final da rota
        $route = rtrim($route, '/');

        // Padrão de validação da URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        // Adiciona a rota dentro da classe
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Responsável por definir uma rota de Get
     *
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = [])
    {
        $this->addRoute('GET', $route, $params);
    }

    /**
     * Responsável por definir uma rota de Post
     *
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = [])
    {
        $this->addRoute('POST', $route, $params);
    }

    /**
     * Responsável por definir uma rota de Put
     *
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = [])
    {
        $this->addRoute('PUT', $route, $params);
    }

    /**
     * Responsável por definir uma rota de Delete
     *
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = [])
    {
        $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Retorna a URI, desconsiderando o prefixo
     *
     * @return string
     */
    private function getUri()
    {
        // URI da request
        $uri = $this->request->getUri();

        // Fatia a URI
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        // Retorna a URI sem prefixo
        return rtrim(end($xUri), '/');
    }

    /**
     * Responsável por retornar os dados da rota atual
     *
     * @return array
     */
    private function getRoute()
    {
        // URI
        $uri = $this->getUri();

        // Método
        $httpMethod = $this->request->getHttpMethod();

        // Valida as rotas
        foreach ($this->routes as $patternRoute => $methods) {
            // Verifica se a URI bate o padrão
            if (preg_match($patternRoute, $uri, $matches)) {
                // Verifica o método
                if (isset($methods[$httpMethod])) {
                    // Remove a primeira posição
                    unset($matches[0]);

                    // Chaves
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    // Retorna os parâmetros da rota
                    return $methods[$httpMethod];
                }

                // Método não encontrado
                throw new Exception("Método não permitido", 405);
            }
        }
        // URL não encontrada
        throw new Exception("URL não encontrada", 404);
    }

    /**
     * Responsável por executar a rota atual
     *
     * @return Response
     */
    public function run()
    {
        try {
            // Pega a rota atual
            $route = $this->getRoute();

            // Veifica se a rota existe
            if (!isset($route['controller'])) {
                throw new Exception("A URL não pôde ser processada", 500);
            }

            // Argumentos da função
            $args = [];

            // Reflection
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            // Retorna a execução da fila
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    /**
     * Responsável por retornar a mensagem de erro
     *
     * @param string $message
     * @return mixed
     */
    private function getErrorMessage($message)
    {
        switch ($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];
                break;

            default:
                return $message;
                break;
        }
    }

    /**
     * Retorna a URL atual
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url . $this->getUri();
    }

    /**
     * Responsável por redirecionar a URL
     *
     * @param string $route
     * @return void
     */
    public function redirect($route)
    {
        // URL
        $url = $this->url . $route;

        // Redireciona o usuário
        header('location: ' . $url);
    }
}
