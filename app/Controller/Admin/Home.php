<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Home extends Page
{
    /**
     * Renderiza a view da home
     *
     * @param Request $request
     * @return string
     */
    public static function getHome($request, $infos = [])
    {
        // Conteúdo da home
        $content = View::render('admin/modules/home/index', [
            'nome' => $_SESSION['admin']['usuario']['nome']
        ]);

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? '', $content, 'home');
    }
}
