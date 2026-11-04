<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DBSellerCacheClear extends Command
{
    protected $signature = 'dbseller:clear';

    protected $description = 'Remove os caches gerados pelo sistema.';

    /**
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $exitCode = Artisan::call('optimize:clear');
        if ($exitCode !== 0) {
            throw new \Exception('Não foi possível limpar os caches do Laravel. ' . Artisan::output());
        }
        $exitCode = Artisan::call('queue:restart');
        if ($exitCode !== 0) {
            throw new \Exception('Não foi possível recarregar os arquivos da fila. ' . Artisan::output());
        }
        \DBMenu::limpaCache();
    }
}
