<?php

namespace App\Domain\Educacao\MatriculaOnline\Middlewares;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class AuthMatriculaOnLineMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (!is_dir("plugins/matricula-on-line") || !file_exists("plugins/matricula-on-line/Manifest.xml")) {
            throw new NotAcceptableHttpException('Plugin Matrícula On Line não instalado.');
        }

        return $next($request);
    }
}
