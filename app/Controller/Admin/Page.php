<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Page
{
    /**
     * Módulos disponíveis no painel
     *
     * @var array
     */
    private static $modules = [
        'home' => [
            'label' => 'Home',
            'link' => URL . '/admin'
        ],
        'posts' => [
            'label' => 'Postagens',
            'link' => URL . '/admin/posts'
        ],
        'categories' => [
            'label' => 'Categorias',
            'link' => URL . '/admin/categories'
        ],
        'users' => [
            'label' => 'Usuários',
            'link' => URL . '/admin/users'
        ]
    ];

    /**
     * Responsável por retornar o conteúdo (view) de página genérica do painel
     *
     * @param string $title
     * @param string $content
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('admin/page', [
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Responsável por renderizar a view do menu
     *
     * @param string $currentModule
     * @return string
     */
    private static function getMenu($currentModule)
    {
        // Links do menu
        $links = '';

        // Itera os modulos
        foreach (self::$modules as $hash => $module) {
            $links .= View::render('admin/menu/link', [
                'label' => $module['label'],
                'link' => $module['link'],
                'current' => $hash == $currentModule ? 'text-danger' : ''
            ]);
        }

        // Retorna a renderização do menu
        return View::render('admin/menu/box', [
            'links' => $links
        ]);
    }

    /**
     * Responsável por renderizar a view do painel com conteúdos dinâmicos
     *
     * @param string $title
     * @param string $content
     * @param string $currentModule
     * @return string
     */
    public static function getPanel($title, $content, $currentModule)
    {
        // Renderiza a view do Painel
        $contentPanel = View::render('admin/panel', [
            'menu' => self::getMenu($currentModule),
            'content' => $content
        ]);

        // Retorna a página renderizada
        return self::getPage($title, $contentPanel);
    }

    /**
     * Método responsável por renderizar o layout de paginação
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        // Seta as páginas
        $pages = $obPagination->getPages();

        // Verifica a quantidade de páginas
        if (count($pages) <= 1) return '';

        // Links
        $links = '';

        // URL atual (Sem Gets)
        $url = $request->getRouter()->getCurrentUrl();

        // Get
        $queryParams = $request->getQueryParams();

        // Renderiza os links
        foreach ($pages as $page) {
            // Altera a página
            $queryParams['page'] = $page['page'];

            // Link
            $link = $url . '?' . http_build_query($queryParams);

            // View
            $links .= View::render('admin/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }

        // Renderização
        return View::render('admin/pagination/box', [
            'links' => $links
        ]);
    }
}
