<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthRedesimMiddleware
 * @package App\Http\Middleware
 */
class AuthRedesimMiddleware
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if (!$request->headers->get("accessKeyId")) {
            Log::warning("[REDESIM Middleware] Header accessKeyId não informado.");
            throw new Exception("Header accessKeyId não informado.");
        }

        if (!env("REDESIM_ACCESS_KEY_ID")) {
            Log::warning("[REDESIM Middleware] Chave REDESIM_ACCESS_KEY_ID não configurada no arquivo .env!");
            throw new Exception("Chave accessKeyId não configurada.");
        }

        if ($request->headers->get("accessKeyId") != env("REDESIM_ACCESS_KEY_ID")) {
            $sLogMessage = "[REDESIM Middleware] Credenciais inválidas ";
            $sLogMessage .= "[header accessKeyId: {$request->headers->get("accessKeyId")}";
            $sLogMessage .= " / Chave REDESIM_ACCESS_KEY_ID: ".env("REDESIM_ACCESS_KEY_ID")."]";

            Log::warning($sLogMessage);
            throw new Exception("Credenciais inválidas.", 401);
        }

        return $next($request);
    }
}
