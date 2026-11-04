<?php

namespace App\Jobs\Tributario\Arrecadacao;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Tributario\Arrecadacao\Services\CancelamentoParcelamentoService;

class CancelamentoParcelamentoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $parcel;
    private $motivo;
    private $processo;
    private $DB_anousu;
    private $DB_instit;
    private $DB_datausu;
    private $DB_id_usuario;
    private $cancelaparcelamento;

    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @var QueuedJob
     */
    public $queuedJob;

    /**
     * The number of times the job may be attempted.
     *
     *  @var int
     */
    public $tries = 1;

    public function __construct(
        $parcel,
        $motivo,
        $processo,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario,
        QueueService $queueService
    ) {
        $this->parcel = $parcel;
        $this->motivo = $motivo;
        $this->processo = $processo;
        $this->DB_anousu = $DB_anousu;
        $this->DB_instit = $DB_instit;
        $this->DB_datausu = $DB_datausu;
        $this->DB_id_usuario = $DB_id_usuario;
        $this->cancelaparcelamento = new CancelamentoParcelamentoService;

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
        $this->cancelaparcelamento->processar(
            $this->parcel,
            $this->motivo,
            $this->processo,
            $this->DB_anousu,
            $this->DB_instit,
            $this->DB_datausu,
            $this->DB_instit
        );

        $this->queueService->terminate($this->queuedJob);

        $existCache = Cache::has('cancelaParcelamentolista');
        if ($existCache) {
            $cache = Cache::get('cancelaParcelamentolista');
            $array = json_decode($cache);
            $jobs = DB::select('select count(1) from queued_jobs where batch_id = ' . $array->id);
            $totaljobs = $jobs[0]->count;

            if ($totaljobs == 0) {
                Cache::forget('cancelaParcelamentolista');
            }
        }
    }
}
