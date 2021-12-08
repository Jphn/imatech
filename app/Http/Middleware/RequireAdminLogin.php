<?php

namespace App\Http\Middleware;

use App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogin
{
    /**
     * Responsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        // Verifica se o usuário está logado, e redireciona se for preciso
        if (!SessionAdminLogin::isLogged()) {
            $request->getRouter()->redirect('/admin/login');
        }

        // Continua a execução
        return $next($request);
    }
}
