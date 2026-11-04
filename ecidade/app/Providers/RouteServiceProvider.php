<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 *
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapUserAuthRoutes();
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapSIMRoutes();
        $this->mapConfiguracaoRoutes();
    }

    protected function mapUserAuthRoutes()
    {
        Route::prefix('v4')->group(function () {
            Route::post('login', 'App\Domain\Configuracao\Usuario\Controller\LoginController@authenticate');
            Route::post('unblock', 'App\Domain\Configuracao\Usuario\Controller\LoginController@unblock');
            Route::put('kill', 'App\Domain\Configuracao\Usuario\Controller\LoginController@kill');
            Route::put('logout', 'App\Domain\Configuracao\Usuario\Controller\LoginController@logout')
                ->middleware(['api', 'auth:api']);
        });
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix('web')->middleware('web')->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('v4/api')->middleware('api')->group(base_path('routes/api.php'));
    }

    private function mapConfiguracaoRoutes()
    {
        Route::prefix('v4/api/configuracao')
//            ->middleware(['api', 'auth:api'])
            ->namespace('App\Domain\Configuracao\\')
            ->group(base_path('routes/api/configuracao/configuracao.php'));
    }

    private function mapSIMRoutes()
    {
        Route::prefix('rest/v1')
            ->middleware(['AuthSim'])
            ->group(base_path('routes/api/sim/seguranca-integrada-municipios.php'));
    }
}
