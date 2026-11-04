<?php

namespace App\Http;

use App\Http\Middleware\AuthSimMiddleware;
use App\Http\Middleware\AuthRedesimMiddleware;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\DatabaseMiddleware;
use App\Http\Middleware\DBRequestMiddleware;
use App\Http\Middleware\LogPhpActivities;
// use App\Http\Middleware\MenuAcessadoMiddleware;
use App\Http\Middleware\PermissoesMenuMiddleware;
use App\Http\Middleware\SessionMiddleware;
use App\Http\Middleware\LegacySessionMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        LogPhpActivities::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        CorsMiddleware::class,
        DBRequestMiddleware::class,
        DatabaseMiddleware::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            CreateFreshApiToken::class,
            \App\Http\Middleware\StartWebSessionMiddleware::class
        ],

        'api' => [
            'throttle:2000,1',
            'bindings',
            SessionMiddleware::class,
            // MenuAcessadoMiddleware::class,
            LogPhpActivities::class
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'legacySession' => LegacySessionMiddleware::class,
        'clientCredential' => CheckClientCredentials::class,
        'AuthSim' => AuthSimMiddleware::class,
        'AuthRedesim' => AuthRedesimMiddleware::class,
        'permissaoMenu' => PermissoesMenuMiddleware::class,
    ];
}
