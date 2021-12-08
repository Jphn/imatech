<?php

namespace App\Session\Admin;

class Login
{
    /**
     * Responsável por iniciar a sessão
     *
     * @return void
     */
    private static function init()
    {
        // Verifica se a sessão já está ativa
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Responsável por criar o login do usuário
     *
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser)
    {
        // Inicia a sessão
        self::init();

        $_SESSION['admin']['usuario'] = [
            'id' => (int)$obUser->id,
            'login' => $obUser->login,
            'nome' => $obUser->nome
        ];

        // Sucesso
        return true;
    }

    /**
     * Confere se o usuário está logado
     *
     * @return boolean
     */
    public static function isLogged()
    {
        // Inicia a sessão para validar
        self::init();

        // Retorna a verificação
        return isset($_SESSION['admin']['usuario']['id']);
    }

    public static function logout()
    {
        // Inicia a sessão
        self::init();

        // Desloga o usuário
        unset($_SESSION['admin']['usuario']);

        return true;
    }
}
