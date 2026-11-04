<?php

namespace App\Console\Commands;

use App\Domain\Tributario\ISSQN\Services\Redesim\AutomaticEstablishmentProcessService;
use DBSeller\Session\Session;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use ECidade\V3\Datasource\Database;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedesimAutomaticEstablishmentProcessTask extends Command
{
    private $automaticEstablishmentProcessService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "tributario:redesim:automaticEstablishmentProcess";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description
        = "Processa os eventos dos estabelecimentos que estão não fila para ser feito automaticamente REDESIM.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AutomaticEstablishmentProcessService $automaticEstablishmentProcessService)
    {
        parent::__construct();

        $this->automaticEstablishmentProcessService = $automaticEstablishmentProcessService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $this->info("Running Task {$this->signature}.");

        $this->loadDependencies();
        $this->loadSession();

        DB::beginTransaction();
        db_inicio_transacao();

        try {
            $this->automaticEstablishmentProcessService->processPendingEstablishments();

            DB::commit();
            db_fim_transacao();

            $this->info("{$this->signature} >>> Establishments processed.");
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

    /**
     * @throws \Exception
     */
    private function loadSession()
    {
        $defaultSession = DefaultSession::getInstance()->start();

        if ($defaultSession->status() !== Session::ACTIVE) {
            throw new AccessDeniedException('Não foi possível construir a sessão de acesso.');
        }

        $defaultSession->set(DefaultSession::DB_MODULO, 1);
        $defaultSession->set(DefaultSession::DB_NOME_MODULO, "Configuração");
        $defaultSession->set(DefaultSession::DB_ACESSADO, 24);
        $defaultSession->set(DefaultSession::DB_ITEMMENU_ACESSADO, 24);
        $defaultSession->set(DefaultSession::DB_REQUEST_FROM_API, true);
        $defaultSession->set(DefaultSession::DB_PROCESSAMENTO_AUTOMATICO, true);

        $databaseSession = DatabaseSession::getInstance()->addSessionToDatabase();

        if (!$databaseSession) {
            throw new NotFoundHttpException('Não foi possível adicionar a sessão ao banco de dados.');
        }
    }
}
