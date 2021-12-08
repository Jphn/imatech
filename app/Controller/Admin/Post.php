<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\Post as EntityPost;
use App\Model\Entity\User as EntityUser;
use App\Model\Entity\Category as EntityCategory;
use WilliamCosta\DatabaseManager\Pagination;

class Post extends Page
{
    /**
     * Guarda o valor do módulo atual
     *
     * @var string
     */
    private static $currentModule = 'posts';

    /**
     * Responsável por obter a renderização dos itens de POSTAGEM da página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getPostItems($request, &$obPagination)
    {
        // Postagens
        $items = '';

        // Quantidade total de registros
        $quantidadeTotal = EntityPost::getPosts(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityPost::getPosts(null, 'id DESC', $obPagination->getLimit());



        // Renderiza o item
        while ($obPost = $results->fetchObject(EntityPost::class)) {
            // Cria os objetos para Categoria e Usuário
            $obUser = EntityUser::getUserById(intval($obPost->idUsuario));
            $obCategory = EntityCategory::getCategoryById(intval($obPost->idCategoria));

            $items .= View::render('admin/modules/posts/item', [
                'id' => $obPost->id,
                'titulo' => $obPost->titulo,
                'conteudo' => $obPost->conteudo,
                'usuario' => $obUser->nome,
                'categoria' => $obCategory->nome,
                'data' => date('d/m/Y h:i:s', strtotime($obPost->data))
            ]);
        }

        // Retorna o valor dos depoimentos
        return $items;
    }

    /**
     * Renderiza a view de listagem de POSTAGEM
     *
     * @param Request $request
     * @return string
     */
    public static function getPosts($request, $infos = [])
    {
        // Conteúdo da home
        $content = View::render('admin/modules/posts/index', [
            'itens' => self::getPostItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
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
                return Alert::getSuccess('Postagem criado com sucesso!');
                break;

            case 'updated':
                return Alert::getSuccess('Postagem atualizado com sucesso!');
                break;

            case 'deleted':
                return Alert::getSuccess('Postagem excluído com sucesso!');
                break;
        }
    }

    /**
     * Responsável por retornar o formulário de cadastro de um novo depoimento
     *
     * @param Resquest $request
     * @return string
     */
    public static function getNewPost($request, $infos = [])
    {
        // Conteúdo do formulário
        $content = View::render('admin/modules/posts/form', array_merge($infos['contentRender'], [
            'options' => self::getOptions($request)
        ]));

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por cadastrar uma nova postagem (Admin)
     *
     * @param Request $request
     * @return string
     */
    public static function setNewPost($request)
    {
        // Pega as variáveis passadas pelo Post
        $postVars = $request->getPostVars();

        // Nova instância de POSTAGEM
        $obPost = new EntityPost;
        $obPost->titulo = $postVars['titulo'];
        $obPost->conteudo = $postVars['conteudo'];
        $obPost->idUsuario = (int)$_SESSION['admin']['usuario']['id'];
        $obPost->idCategoria = (int)$postVars['categoria'];
        $obPost->available = isset($postVars['visibilidade']) ? (bool)$postVars['visibilidade'] :  false;
        $obPost->cadastrar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/posts/' . $obPost->id . '/edit?status=created');
    }

    /**
     * Retorna as opções para serem renderizadas
     *
     * @param Request $request
     * @return string
     */
    private static function getOptions($request, $id = null)
    {
        // Estabelece a váriavel dos Itens
        $items = '';

        // Coisa pra não bugar o código (Não sei porquê)
        $results = EntityCategory::getCategories(null, 'id ASC');

        // Realiza a atribuição e substituição
        while ($obCategory = $results->fetchObject(EntityCategory::class)) {
            $items .= View::render('admin/modules/posts/option', [
                'value' => (int)$obCategory->id,
                'label' => $obCategory->nome,
                'this' => $id === (int)$obCategory->id ? 'selected' : ''
            ]);
        }

        // Retorna as opções
        return $items;
    }

    /**
     * Responsável por retornar a página de edição de uma postagem específica
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditPost($request, $id, $infos = [])
    {
        // Obtém o depoimento do BD
        $obPost = EntityPost::getPostById($id);

        // Valida a instância
        if (!$obPost instanceof EntityPost) {
            $request->getRouter()->redirect('/admin/posts');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/posts/form', array_merge($infos['contentRender'], [
            'titulo' => $obPost->titulo,
            'conteudo' => $obPost->conteudo,
            'avaiable' => (bool)$obPost->available ? 'checked' : '',
            'status' => self::getStatus($request),
            'options' => self::getOptions($request, (int)$obPost->idCategoria)
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por salvar a atualização de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditPost($request, $id)
    {
        // Obtém o depoimento do BD
        $obPost = EntityPost::getPostById($id);

        // Valida a instância
        if (!$obPost instanceof EntityPost) {
            $request->getRouter()->redirect('/admin/posts');
            exit;
        }

        // Variáveis do Post
        $postVars = $request->getPostVars();

        // Atualiza a instância
        $obPost->titulo = $postVars['titulo'] ?? $obPost->titulo;
        $obPost->conteudo = $postVars['conteudo'] ?? $obPost->conteudo;
        $obPost->idCategoria = $postVars['categoria'] ?? $obPost->idCategoria;
        $obPost->available = $postVars['visibilidade'] ?? false;
        $obPost->atualizar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/posts/' . $obPost->id . '/edit?status=updated');
    }

    /**
     * Responsável por retornar a página de exclusão de uma postagem
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeletePost($request, $id, $infos = [])
    {
        // Obtém o depoimento do BD
        $obPost = EntityPost::getPostById($id);

        // Valida a instância
        if (!$obPost instanceof EntityPost) {
            $request->getRouter()->redirect('/admin/posts');
            exit;
        }

        $obUser = EntityUser::getUserById($obPost->idUsuario);

        // Conteúdo do formulário
        $content = View::render('admin/modules/posts/delete', array_merge($infos['contentRender'], [
            'titulo' => $obPost->titulo,
            'usuario' => $obUser->nome
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por realizar a exclusão de uma postagem
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeletePost($request, $id)
    {
        // Obtém o depoimento do BD
        $obPost = EntityPost::getPostById($id);

        // Valida a instância
        if (!$obPost instanceof EntityPost) {
            $request->getRouter()->redirect('/admin/posts');
            exit;
        }

        // Realiza a exclusão
        $obPost->excluir();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/posts?status=deleted');
    }
}
