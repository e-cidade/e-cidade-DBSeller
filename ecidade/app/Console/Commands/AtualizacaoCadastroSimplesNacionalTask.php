<?php

namespace App\Console\Commands;

use App\Domain\Tributario\ISSQN\Services\SimplesNacional\AtualizaCadastroOptantesSimplesNacionalService;
use Illuminate\Console\Command;

class AtualizacaoCadastroSimplesNacionalTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tributario:atualizacaocadastrosimplesnacional';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para sincronizar cadastros de simples nacional com api da Receita Federal';

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
        $service = AtualizaCadastroOptantesSimplesNacionalService::factory();
        $service->execute();
    }
}
