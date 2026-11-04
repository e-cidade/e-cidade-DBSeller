<?php

namespace App\Console\Commands;

use App\Domain\Tributario\ISSQN\Services\Redesim\RedesimService;
use ECidade\V3\Datasource\Database;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RedesimRetrieveEstablishmentsTask extends Command
{
    private $redesimService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "tributario:redesim:retrieveEstablishments";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Recupera os estabelecimentos cadastrados na REDESIM.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RedesimService $redesimService)
    {
        parent::__construct();

        $this->redesimService = $redesimService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Running Task {$this->signature}.");

        $this->loadDependencies();

        DB::beginTransaction();
        db_inicio_transacao();

        try {
            $this->redesimService->retrieveEstablishments();

            DB::commit();
            db_fim_transacao();

            $this->info("{$this->signature} >>> Establishments retrieved.");
        } catch (\Exception $exception) {
            DB::rollback();
            db_fim_transacao(true);

            $this->error("Error to run task {$this->signature} >>> {$exception->getMessage()}");
        }

        $this->info("Task {$this->signature} finalized.");

        return true;
    }

    private function loadDependencies()
    {
        global $conn;
        $conn = Database::getInstance(true)->connect();

        require_once(modification("libs/db_stdlib.php"));
        require_once(modification("dbforms/db_funcoes.php"));
    }
}
