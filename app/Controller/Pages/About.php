<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class About extends Page
{
    /**
     *Retorna o conteúdo
     *
     * @return string
     */
    public static function getAbout($infos = [])
    {
        // Organização
        $obOrganization = new Organization;

        // View do Sobre
        $content = View::render('pages/about', [
            'site' => $obOrganization->site,
            'description' => $obOrganization->description
        ]);

        // Retorna a View da página
        return parent::getPage($infos['title'] ?? null, $content);
    }
}
