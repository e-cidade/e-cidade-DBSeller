<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Core\Models\BatchJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Saude\Farmacia\Models\BnafarBatch;
use App\Domain\Saude\Farmacia\Models\BnafarBatchProtocolo;
use App\Jobs\Saude\Farmacia\ExportarBnafarJob;
use Carbon\Carbon;

class BnafarQueueService
{
    /**
     * @var QueueService[]
     */
    private $queueService = [];

    /**
     * @param int $tipo
     * @return void
     */
    public function newBatch($tipo)
    {
        $this->queueService[$tipo] = new QueueService(ExportarBnafarJob::class);
        $service = $this->queueService[$tipo];

        BnafarBatch::create(['fa75_batch' => $service->getBatch()->id, 'fa75_tipo' => $tipo]);
    }

    /**
     * @param int $tipo
     * @return QueueService
     */
    public function getQueueService($tipo)
    {
        return $this->queueService[$tipo];
    }

    /**
     * @return void
     */
    public function terminate()
    {
        foreach ($this->queueService as $queueService) {
            $this->markAsDone($queueService->getBatch());
            $queueService->cancel();
        }
    }

    /**
     * @param BatchJob $batchJob
     * @return void
     */
    public static function markAsDone(BatchJob $batchJob)
    {
        $batch = BnafarBatch::where('fa75_batch', $batchJob->id)->first();
        $batch->fa75_concluido = true;
        $batch->save();
    }

    /**
     * @param int $tipo
     * @return int
     */
    public static function getNumProcess($tipo)
    {
        $batch = BnafarBatch::where('fa75_tipo', $tipo)->where('fa75_concluido', false)->first();
        if (!$batch) {
            return 0;
        }

        return $batch->batch->queuedJobs()->count() ?: 1;
    }

    /**
     * @param BatchJob $batchJob
     * @param $protocolo
     * @return void
     */
    public static function newProtocolo(BatchJob $batchJob, $protocolo)
    {
        $batch = BnafarBatch::where('fa75_batch', $batchJob->id)->first();

        $bnafarProtocolo = new BnafarBatchProtocolo();
        $bnafarProtocolo->fa76_bnafarbatch = $batch->fa75_id;
        $bnafarProtocolo->fa76_protocolo = $protocolo;
        $bnafarProtocolo->fa76_created_at = Carbon::now();
        $bnafarProtocolo->save();
    }

    /**
     * @param int $protocolo
     * @return void
     */
    public static function markAsFailed(BatchJob $batchJob, $protocolo)
    {
        $batchProtocolo = BnafarBatchProtocolo::where('fa76_protocolo', $protocolo)
            ->whereHas('bnafarBatch', function ($query) use ($batchJob) {
                $query->where('fa75_batch', $batchJob->id);
            })->first();

        $batchProtocolo->fa76_falhou = true;
        $batchProtocolo->save();
    }
}
