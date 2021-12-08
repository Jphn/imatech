<?php

namespace App\Controller\Pages;

use App\Utils\View;

class Page
{

    /**
     * Responsável por retornar o Header da página
     *
     * @return string
     */
    private static function getHeader()
    {
        return View::render('pages/header');
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
            $links .= View::render('pages/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : ''
            ]);
        }

        // Renderização
        return View::render('pages/pagination/box', [
            'links' => $links
        ]);
    }

    /**
     * Responsável por retornar o Footer da página
     *
     * @return string
     */
    private static function getFooter()
    {
        return View::render('pages/footer', [
            'ano' => date('Y')
        ]);
    }

    /**
     * Pega a página template
     *
     * @param string $tilte
     * @param string $content
     * @return string
     */
    public static function getPage($tilte, $content)
    {
        return View::render('pages/page', [
            'title' => $tilte,
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter()

        ]);
    }
}
