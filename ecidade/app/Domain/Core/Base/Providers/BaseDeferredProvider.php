<?php

namespace App\Domain\Core\Base\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseDeferredProvider extends ServiceProvider
{
    /**
     * Indica que o carregamento do provider é adiado.
     * @var bool
     */
    protected $defer = true;

    /**
     * Registra o service provider.
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
    }

    /**
     * Retorna os Services registrados no provider.
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
