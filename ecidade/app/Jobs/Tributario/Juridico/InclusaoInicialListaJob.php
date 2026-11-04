<?php

namespace App\Jobs\Tributario\Juridico;

use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Tributario\Juridico\Services\InclusaoInicialServices;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InclusaoInicialListaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cda;
    private $observacao;
    private $v50_advog;
    private $v50_codlocal;
    private $DB_anousu;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;
    private $DB_acessado;
    private $gera;
    private $cert_ant;

    /**
     * The number of times the job may be attempted.
     *
     *  @var int
     */
    public $tries = 3;

    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @var QueuedJob
     */
    public $queuedJob;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $cda,
        $observacao,
        $v50_advog,
        $v50_codlocal,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario,
        $DB_acessado,
        QueueService $queueService,
        $gera,
        $cert_ant
    ) {
        $this->cda           = $cda;
        $this->observacao    = $observacao;
        $this->v50_advog     = $v50_advog;
        $this->v50_codlocal  = $v50_codlocal;
        $this->DB_anousu     = $DB_anousu;
        $this->DB_instit     = $DB_instit;
        $this->DB_datausu    = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->DB_acessado   = $DB_acessado;
        $this->gera = $gera;
        $this->cert_ant   = $cert_ant;

        $this->queueService = $queueService;
        $this->queuedJob = $queueService->next();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->queuedJob->refresh();
        if ($this->queuedJob->batch->cancelled) {
            return;
        }

        if (!$this->gera) {
            $clinicialcert   = new \cl_inicialcert;
            $time = 0;
            do {
                if ($time > 0) {
                    sleep(1);
                }
                if ($time > 60) {
                    throw new Exception('Inicial não encontrada cda:' . $this->cert_ant);
                }
                $sSqlInicialCert  =  $clinicialcert->sql_query_file(null, $this->cert_ant);
                $clinicialcert->sql_record($sSqlInicialCert);
                $time++;
            } while ($clinicialcert->numrows == 0);
        }

        $InclusaoInicialServices = new InclusaoInicialServices(
            $this->DB_anousu,
            $this->DB_instit,
            $this->DB_datausu,
            $this->DB_id_usuario,
            $this->DB_acessado
        );
        $InclusaoInicialServices->incluir(
            $this->cda,
            $this->observacao,
            $this->v50_advog,
            $this->v50_codlocal,
            $this->gera,
            $this->cert_ant
        );

        $this->queueService->terminate($this->queuedJob);

        $existCache = Cache::has('inclusaoiniciallista');
        if ($existCache) {
            $cache = Cache::get('inclusaoiniciallista');
            $array = json_decode($cache);
            $jobs = DB::select('select count(1) from queued_jobs where batch_id = ' . $array->id);
            $totaljobs = $jobs[0]->count;

            if ($totaljobs == 0) {
                $InclusaoInicialServices->notificaUsuario($this->observacao);
                Cache::forget('inclusaoiniciallista');
            }
        }
    }
}
