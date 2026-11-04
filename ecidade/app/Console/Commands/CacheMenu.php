<?php

namespace App\Console\Commands;

use App\Domain\Configuracao\Menu\Services\CacheMenuService;
use Exception;
use Illuminate\Console\Command;

class CacheMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'configuracao:cache-menu {--clear : Limpar o cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza o cache do menu em arquivo estatico.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $menuCacheService = new CacheMenuService();

        if ($this->option('clear')) {
            $this->info('Limpando o cache...');
            $menuCacheService->clearAll();
            return;
        }

        try {
            $this->info('Realizando cache das areas...');
            $menuCacheService->cacheAreas();

            $this->info('Realizando cache dos modulos...');
            $menuCacheService->cacheModulos();

            $this->info('Realizando cache dos itens...');
            $menuCacheService->cacheItens();
        } catch (Exception $e) {
            $menuCacheService->clearAll();
            $this->error($e->getMessage());
        }
    }
}
