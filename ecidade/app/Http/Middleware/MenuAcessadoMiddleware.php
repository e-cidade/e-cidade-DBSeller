<?php

namespace App\Http\Middleware;

use Closure;
use ECidade\Api\V1\Middleware\Session\MenuAcessadoSessionMiddleware;
use Exception;
use Illuminate\Http\Request;

/**
 * Class MenuAcessadoMiddleware
 *
 * @package App\Http\Middleware
 */
class MenuAcessadoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        MenuAcessadoSessionMiddleware::setMenuAcessado($request);

        return $next($request);
    }
}
