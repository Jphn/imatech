<?php

namespace App\Utils;

class View
{
    /**
     * Variáveis padrões da View
     *
     * @var array
     */
    private static $vars = [];

    /**
     * Responsável por definir os dados iniciais da classe
     *
     * @param array $vars
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    /**
     * Pega o conteúdo HTML, retornado o arquivo.
     *
     * @param string $view
     * @return string
     */
    public static function getContentView($view)
    {
        $file = __DIR__ . '/../../resources/view/' . $view . '.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Renderiza as informações do HTML na tela do Index PHP.
     *
     * @param string $view
     * @param array $vars
     * @return string
     */
    public static function render($view, $vars = [])
    {
        // Conteúdo da View
        $contentView = self::getContentView($view);

        // Mescla de variáveis da view
        $vars = array_merge(self::$vars, $vars);

        // Chaves do Vetor
        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);

        // Retorna o View trocando as marcações no HTML
        return str_replace($keys, array_values($vars), $contentView);
    }
}
