<?php

namespace App\Jobs\Tributario\Juridico;

use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Tributario\Juridico\Services\AnulaCDAServices;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CancelamentoCDALista implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cda;
    private $observacao;
    private $inicial;
    private $DB_anousu;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;
    private $DB_acessado;

    /**
     * Create a new AnulaCDAServices instance.
     *
     * @return void
     */
    private $anulacaoServices;

    /**
     * The number of times the job may be attempted.
     *
     *  @var int
     */
    public $tries = 1;

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
        $inicial,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario,
        $DB_acessado,
        QueueService $queueService
    ) {
        $this->cda           = $cda;
        $this->observacao    = $observacao;
        $this->inicial       = $inicial;
        $this->DB_anousu     = $DB_anousu;
        $this->DB_instit     = $DB_instit;
        $this->DB_datausu    = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->DB_acessado   = $DB_acessado;

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

        $this->anulacaoServices = new AnulaCDAServices(
            $this->cda,
            $this->observacao,
            $this->inicial,
            $this->DB_anousu,
            $this->DB_instit,
            $this->DB_datausu,
            $this->DB_id_usuario,
            $this->DB_acessado
        );

        $this->anulacaoServices->anula();

        $this->queueService->terminate($this->queuedJob);

        $existCache = Cache::has('anulacaocda');
        if ($existCache) {
            $cache = Cache::get('anulacaocda');
            $array = json_decode($cache);
            $jobs = DB::select('select count(1) from queued_jobs where batch_id = ' . $array->id);
            $totaljobs = $jobs[0]->count;

            if ($totaljobs == 0) {
                $this->anulacaoServices->notificaUsuario();
                Cache::forget('anulacaocda');
            }
        }
    }
}
