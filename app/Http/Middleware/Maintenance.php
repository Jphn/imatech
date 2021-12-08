<?php

namespace App\Http\Middleware;

class Maintenance
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
        // Verifica o estado de manutenção da página
        if (filter_var(getenv('MAINTENANCE'), FILTER_VALIDATE_BOOLEAN)) {
            throw new \Exception("Página em manutenção. Tente novamente mais tarde.", 200);
        }

        // Executa o próximo nível do middleware
        return $next($request);
    }
}