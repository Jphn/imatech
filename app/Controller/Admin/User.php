<?php

namespace App\Controller\Admin;

use App\Utils\View;
use App\Model\Entity\User as EntityUser;
use WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{
    /**
     * Define o valor do módulo atual
     *
     * @var string
     */
    private static $currentModule = 'users';

    /**
     * Responsável por obter a renderização dos itens de usuários da página
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItems($request, &$obPagination)
    {
        // Usuários
        $itens = '';

        // Quantidade total de registros
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // Página atual
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // Instância de paginação
        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

        // Resultados da página
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item
        while ($obUser = $results->fetchObject(EntityUser::class)) {
            $itens .= View::render('admin/modules/users/item', [
                'id' => $obUser->id,
                'login' => $obUser->login,
                'nome' => $obUser->nome
            ]);
        }

        // Retorna o valor dos usuários
        return $itens;
    }

    /**
     * Renderiza a view de listagem de usuários
     *
     * @param Request $request
     * @return string
     */
    public static function getUsers($request, $infos = [])
    {
        // Conteúdo da home
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por retornar o formulário de cadastro de um novo usuário
     *
     * @param Resquest $request
     * @return string
     */
    public static function getNewUser($request, $infos = [])
    {
        // Conteúdo do formulário
        $content = View::render('admin/modules/users/form', array_merge($infos['contentRender'], [
            'status' => self::getStatus($request)
        ]));

        // Retorna a página completa
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por cadastrar um novo usuário (Admin)
     *
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        // Pega as variáveis passadas pelo Post
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? null;
        $login = $postVars['login'] ?? null;
        $senha = $postVars['senha'] ?? null;

        // Valida o login
        $obUser = EntityUser::getUserByLogin($login);
        if ($obUser instanceof EntityUser) {
            // Redirecionamento
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
            exit;
        }

        // Nova instância de usuário
        $obUser = new EntityUser;
        $obUser->login = $login;
        $obUser->nome = $nome;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->cadastrar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=created');
    }

    /**
     * Responsável por retornar a mensagem de status
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
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;

            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;

            case 'deleted':
                return Alert::getSuccess('Usuário excluído com sucesso!');
                break;

            case 'duplicated':
                return Alert::getError('O login digitado já está sendo utilizado!');
                break;
        }
    }

    /**
     * Responsável por retornar a página de edição de um usuário específico
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id, $infos = [])
    {
        // Obtém o usuário do BD
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
            exit;
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/users/form', array_merge($infos['contentRender'], [
            'nome' => $obUser->nome,
            'login' => $obUser->login,
            'status' => self::getStatus($request)
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por salvar a atualização de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return void
     */
    public static function setEditUser($request, $id)
    {
        // Obtém o usuário do BD
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Variáveis do Post
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $login = $postVars['login'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // Verifica se o login já foi cadastrado em outro usuário
        $obUserLogin = EntityUser::getUserByLogin($login);

        if ($obUserLogin instanceof EntityUser and $obUserLogin->id != $id) {
            // Redirecionamento
            $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=duplicated');
            exit;
        }

        // Atualiza a instância
        $obUser->login = $login;
        $obUser->nome = $nome;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=updated');
    }

    /**
     * Responsável por retornar a página de exclusão de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id, $infos = [])
    {
        // Obtém o usuário do BD
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
        }

        // Conteúdo do formulário
        $content = View::render('admin/modules/users/delete', array_merge($infos['contentRender'], [
            'nome' => $obUser->nome,
            'login' => $obUser->login
        ]));

        // Retorna a página
        return parent::getPanel($infos['title'] ?? '', $content, self::$currentModule);
    }

    /**
     * Responsável por realizar a exclusão de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request, $id)
    {
        // Obtém o usuário do BD
        $obUser = EntityUser::getUserById($id);

        // Valida a instância
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/users');
            exit;
        }

        // Realiza a exclusão
        $obUser->excluir();

        // Redirecionamento
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}
