<?php

namespace App\Jobs\RecursosHumanos\Pessoal;

use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\ContraChequePdf;
use App\Domain\RecursosHumanos\Pessoal\Services\Contracheque\EnviarContrachequeProcessoEletronico;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmissaoContraChequeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ContraChequePdf
     */
    private $contraChequePdf;
    /**
     * @var QueuedJob
     */
    private $queuedJob;

    /**
     * @var integer
     */
    public $tries = 1;

    public $timeout = 120;
    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @return QueuedJob
     */
    public function getQueuedJob()
    {
        return $this->queuedJob;
    }

    /**
     * @return ContraChequePdf
     */
    public function getContraChequePdf()
    {
        return $this->contraChequePdf;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        ContraChequePdf $contraChequePdf,
        QueueService $queueService
    ) {
        $this->contraChequePdf = $contraChequePdf;
        $this->queueService    = $queueService;
        $this->queuedJob       = $queueService->next();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $this->queuedJob->refresh();
        if ($this->queuedJob->batch->cancelled) {
            return;
        }

        $this->queuedJob->payload        = $this->job->getRawBody();
        $this->queuedJob->queue          = $this->job->getQueue();
        $this->queuedJob->job_connection = $this->job->getConnectionName();
        $this->queuedJob->save();

        try {
            $this->dependencias();
            DB::beginTransaction();
            db_inicio_transacao();
            $this->verificaCacheAutenticacaoEstorage();
            $this->contraChequePdf->emitir();
            $this->gerarCacheAutenticacaoEstorage();
            db_fim_transacao();
            DB::commit();
            $enviado = (new EnviarContrachequeProcessoEletronico(
                $this->contraChequePdf->getMatricula(),
                $this->contraChequePdf->getInstituicao(),
                $this->contraChequePdf->getMes(),
                $this->contraChequePdf->getAno()
            ))->execute();
            $inQueue = $this->queueService->terminate($this->queuedJob);
        } catch (Exception $exception) {
            db_fim_transacao(true);
            DB::rollback();
            throw $exception;
        }
    }

    private function verificaCacheAutenticacaoEstorage()
    {
        if (! empty(Cache::get('estorage_properties'))) {
            $_SESSION['estorage_properties']
                = unserialize(Cache::get('estorage_properties'));
        }
    }

    private function gerarCacheAutenticacaoEstorage()
    {
        if (isset($_SESSION['estorage_properties'])) {
            Cache::put(
                'estorage_properties',
                serialize($_SESSION['estorage_properties']),
                10
            );
            unset($_SESSION['estorage_properties']);
        }
    }

    private function dependencias()
    {
        if (! defined('DB_BIBLIOT')) {
            define('DB_BIBLIOT', 1);
        }
        if (! defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', 'fpdf151/font/');
        }
        require_once(modification('fpdf151/fpdf.php'));
        require_once(modification("dbforms/db_funcoes.php"));
        $_SESSION["DB_ip"]         = '127.0.0.1';
        $_SESSION["DB_datausu"]    = time();
        $_SESSION["DB_id_usuario"] = "1";
        $_SESSION["DB_acessado"]   = 9433155;
    }


    public function failed($exception)
    {
        $this->queuedJob->failed_at = date('Y-m-d H:i:s');
        $this->queuedJob->exception = $exception->getMessage()." "
                                      .$exception->getTraceAsString();
        $this->queuedJob->save();
    }
}
