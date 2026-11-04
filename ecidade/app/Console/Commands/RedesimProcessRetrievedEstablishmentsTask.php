<?php

namespace App\Console\Commands;

use App\Domain\Tributario\ISSQN\Services\Redesim\RedesimService;
use DBSeller\Session\Session;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use ECidade\V3\Datasource\Database;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedesimProcessRetrievedEstablishmentsTask extends Command
{
    private $redesimService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tributario:redesim:processRetrievedEstablishments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa os estabalecimentos recuperados na REDESIM.';

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
        $this->loadSession();

        try {
            $this->redesimService->processEstablishments();
        } catch (\Exception $exception) {
            $this->error("Error to run task {$this->signature} [{$exception->getMessage()}]");
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
