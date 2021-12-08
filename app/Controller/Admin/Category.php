<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Category as EntityCategory;
use App\Model\Entity\Post as EntityPost;
use WilliamCosta\DatabaseManager\Pagination;

class Category extends Page
{
    /** 
     * Define o valor do módulo atual
     *
     * @var string
     */
    private static $currentModule = 'categories';

    /**
     * Responsável por obter a renderização dos itens de POSTAGEM da página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getCategoryItems($request, &$obPagination)
    {
        // Postagens
        $items = '';

        // Quantidade total de registros
        $quantidadeTotal = EntityCategory::getCategories(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 10);

        // Resultados da página
        $results = EntityCategory::getCategories(null, 'id DESC', $obPagination->getLimit());



        // Renderiza o item
        while ($obCategory = $results->fetchObject(EntityCategory::class)) {
            $items .= View::render('admin/modules/categories/item', [
                'id' => $obCategory->id,
                'nome' => $obCategory->nome,
                'postagens' => $obCategory->contagem()
            ]);
        }

        // Retorna o valor dos depoimentos
        return $items;
    }

    /**
     * Retorna a rendirização da listagem de CATEGORIAS
     *
     * @param Request $request
     * @return string
     */
    public static function getCategories($request, $infos = [])
    {
        // Conteúdo da home
        $content = View::render('admin/modules/categories/index', [
            'itens' => self::getCategoryItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? null, $content, self::$currentModule);
    }

    /**
     * Responsável por retornar a conteudo de status
     *
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        // Query Params
        $queryParams = $request->getQueryParams();

        // Checa o status
        if (!isset($queryParams['status'])) return '';

        // Mensagem de status
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Categoria criado com sucesso!');
                break;

            case 'updated':
                return Alert::getSuccess('Categoria atualizado com sucesso!');
                break;

            case 'deleted':
                return Alert::getSuccess('Categoria excluído com sucesso!');
                break;

            case 'error':
                return Alert::getError('Você não possui permissão para apagar essa categoria!');
                break;
        }
    }

    /**
     * Responsável por retornar o formulário de cadastro de uma nova categoria
     *
     * @param Resquest $request
     * @return string
     */
    public static function getNewCategory($request, $infos = [])
    {
        // Conteúdo do formulário
        $content = View::render('admin/modules/categories/form', array_merge($infos['contentRender'], []));

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? null, $content, self::$currentModule);
    }

    /**
     * Responsável por cadastrar uma nova postagem (Admin)
     *
     * @param Request $request
     * @return string
     */
    public static function setNewCategory($request)
    {
        // Pega as variáveis passadas pelo Category
        $postVars = $request->getPostVars();

        // Nova instância de POSTAGEM
        $obCategory = new EntityCategory;
        $obCategory->nome = $postVars['nome'];
        $obCategory->cadastrar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/categories/' . $obCategory->id . '/edit?status=created');
    }

    /**
     * Responsável por retornar a página de edição de uma postagem específica
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditCategory($request, $id, $infos = [])
    {
        // Obtém o depoimento do BD
        $obCategory = EntityCategory::getCategoryById($id);

        // Valida a instância
        if (!$obCategory instanceof EntityCategory) {
            $request->getRouter()->redirect('/admin/categories');
            exit;
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/categories/form', array_merge($infos['contentRender'], [
            'nome' => $obCategory->nome,
            'status' => self::getStatus($request)
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? null, $content, self::$currentModule);
    }

    /**
     * Responsável por salvar a atualização de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditCategory($request, $id)
    {
        // Obtém o depoimento do BD
        $obCategory = EntityCategory::getCategoryById($id);

        // Valida a instância
        if (!$obCategory instanceof EntityCategory) {
            $request->getRouter()->redirect('/admin/categories');
            exit;
        }

        // Variáveis do Post
        $postVars = $request->getPostVars();

        // Atualiza a instância
        $obCategory->nome = $postVars['nome'] ?? $obCategory->nome;
        $obCategory->atualizar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/categories/' . $obCategory->id . '/edit?status=updated');
    }

    /**
     * Responsável por retornar a página de exclusão de uma postagem
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteCategory($request, $id, $infos = [])
    {
        // Obtém o depoimento do BD
        $obCategory = EntityCategory::getCategoryById($id, true);

        // Valida a instância
        if (!$obCategory instanceof EntityCategory) {
            $request->getRouter()->redirect('/admin/categories?status=error');
            exit;
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/categories/delete', array_merge($infos['contentRender'], [
            'nome' => $obCategory->nome,
            'numero' => $obCategory->contagem()
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? null, $content, self::$currentModule);
    }

    /**
     * Responsável por realizar a exclusão de uma postagem
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteCategory($request, $id)
    {
        // Obtém o depoimento do BD
        $obCategory = EntityCategory::getCategoryById($id, true);

        // Valida a instância
        if (!$obCategory instanceof EntityCategory) {
            $request->getRouter()->redirect('/admin/categories?status=error');
        }

        // Realiza a exclusão
        $obCategory->excluir();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/categories?status=deleted');
    }
}
