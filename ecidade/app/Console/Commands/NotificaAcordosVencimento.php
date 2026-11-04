<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Domain\Patrimonial\Contratos\Services\NotificarAcordoVencimentoService;

class NotificaAcordosVencimento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbseller:notifica-acordos-vencimento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checa acordos de vencimento e notifica';

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
        NotificarAcordoVencimentoService::notificarAcordoVencimento();
    }
}
