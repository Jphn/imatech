<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class Home extends Page
{
    /**
     *Retorna o conteúdo
     *
     * @return string
     */
    public static function getHome($infos = [])
    {
        // Organização
        $obOrganization = new Organization;

        // View da Home
        $content = View::render('pages/home', [
            'name' => $obOrganization->name
        ]);

        // Retorna a View da página
        return parent::getPage($infos['title'] ?? null, $content);
    }
}
