<?php

namespace App\Http\Middleware;

class Queue
{

    /**
     * Mapeamento de middlewares
     *
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento de middlewares que vão ser carregados em todas as rotas
     *
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares a ser executada
     *
     * @var array
     */
    private $middlewares = [];

    /**
     * Executa o controlador
     *
     * @var callable Closure
     */
    private $controller;

    /**
     * Argumentos da funcão do controlador
     *
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Contrói a classe de fila de middlewares
     *
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Responsável por definir o mapeamento de middlewares
     *
     * @param array $map
     * @return void
     */
    public static function setMap($map)
    {
        self::$map = $map;
    }

    /**
     * Responsável por definir o mapeamento de middlewares padrões (Executados em todas as rotas)
     *
     * @param array $default
     * @return void
     */
    public static function setDefault($default)
    {
        self::$default = $default;
    }

    /**
     * Responsável por executar o próximo nível da fila de middlewares
     *
     * @param Request $request
     * @return void
     */
    public function next($request)
    {
        // Verifica se a fila está vazia
        if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        // Middlewares
        $middleware = array_shift($this->middlewares);

        // Verifica o mapeamento
        if (!isset(self::$map[$middleware])) {
            throw new \Exception("Problemas aos processar o middleware da requisição.", 500);
        }

        // Next
        $queue = $this;
        $next = function ($request) use ($queue) {
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request, $next);
    }
}
