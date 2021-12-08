<?php

namespace App\Controller\Admin;

use App\Model\Entity\User;
use App\Utils\View;
use App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page
{
    /**
     * Responsável por retornar a renderização da página de login
     *
     * @param Request $request
     * @return string
     */
    public static function getLogin($request, $errorMessage = null, $infos = [])
    {
        // Status
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : Alert::getWarning('Login: usuarioTeste <br> Senha: teste');

        $content = View::render('admin/login', [
            'status' => $status
        ]);

        return parent::getPage($infos['title'] ?? '', $content);
    }

    /**
     * Responsável por definir o login do usuário
     *
     * @param Request $request
     * @return void
     */
    public static function setLogin($request)
    {
        // Post Vars
        $postVars = $request->getPostVars();
        $login = $postVars['login'] ?? '';
        $senha = $postVars['senha'] ?? '';

        // Busca o usuário pelo email, e verifica a senha
        $obUser = User::getUserByLogin($login);
        if (!$obUser instanceof User or !password_verify($senha, $obUser->senha)) {
            return self::getLogin($request, 'Email ou senha inválidos.');
        }

        // Verifica a senha
        // if (!password_verify($senha, $obUser->senha)) {
        //     return self::getLogin($request, 'Email ou senha inválidos.');
        // }

        // Cria a sessão de login
        SessionAdminLogin::login($obUser);

        // Realiza o redirecionamento para a página do admin
        $request->getRouter()->redirect('/admin');
    }

    /**
     * Responsável por deslogar o usuário
     *
     * @param Request $request
     * @return void
     */
    public static function setLogout($request)
    {
        // Cria a sessão de login
        SessionAdminLogin::logout();

        // Realiza o redirecionamento para a página do admin
        $request->getRouter()->redirect('/admin/login');
    }
}
