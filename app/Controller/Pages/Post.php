<?php

namespace App\Controller\Pages;

use WilliamCosta\DatabaseManager\Pagination;
use App\Model\Entity\Post as EntityPost;
use App\Utils\View;

class Post extends Page
{
    /**
     * Responsável por obter os itens de depoimento
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getPostItems($request, &$obPagination)
    {
        // Postagens
        $itens = '';

        // Quantidade total de registros
        $quantidadeTotal = EntityPost::getPosts("available = 1", null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 3);

        // Resultados da página
        $results = EntityPost::getPosts("available = 1", 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obPost = $results->fetchObject(EntityPost::class)) {
            $itens .= View::render('pages/post/item', [
                'titulo' => $obPost->titulo,
                'conteudo' => $obPost->conteudo,
                'data' => date('d/m/Y h:i:s', strtotime($obPost->data))
            ]);
        }

        // Retorna o valor das postagens
        return $itens;
    }

    /**
     * Retorna as postagens já renderizadas
     *
     * @param Request $request
     * @return string
     */
    public static function getPosts($request, $infos = [])
    {
        // View de Depoimentos
        $content = View::render('pages/posts', [
            'itens' => self::getPostItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ]);

        // Retorna a View da página
        return parent::getPage($infos['title'] ?? null, $content);
    }

    /**
     * Salva a nova postagem no banco de dados
     *
     * @param Request $request
     * @return string
     */
    public static function setPost($request)
    {
        $request->getRouter()->redirect('/blog?page=1');
        exit;

        // Parâmetros
        $postVars = $request->getPostVars();

        // Objeto Post
        $obPost = new EntityPost;
        $obPost->titulo = $postVars['titulo'];
        $obPost->conteudo = $postVars['conteudo'];
        $obPost->idUsuario = 1;
        $obPost->idCategoria = 1;
        $obPost->available = true;

        $obPost->cadastrar();

        return self::getPosts($request);
    }
}
