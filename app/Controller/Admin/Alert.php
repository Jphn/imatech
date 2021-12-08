<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert
{
    /**
     * Retorna uma mensagem de erro
     *
     * @param string $message
     * @return string
     */
    public static function getError($message)
    {
        return View::render('admin/alert/status', [
            'tipo' => 'danger',
            'mensagem' => $message
        ]);
    }

    /**
     * Retorna uma mensagem de sucesso
     *
     * @param string $message
     * @return string
     */
    public static function getSuccess($message)
    {
        return View::render('admin/alert/status', [
            'tipo' => 'success',
            'mensagem' => $message
        ]);
    }

    /**
     * Retorna uma mensagem de sucesso
     *
     * @param string $message
     * @return string
     */
    public static function getWarning($message)
    {
        return View::render('admin/alert/status', [
            'tipo' => 'warning',
            'mensagem' => $message
        ]);
    }
}
