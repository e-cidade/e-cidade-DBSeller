<?php

namespace App\Jobs\Saude\Farmacia;

use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Saude\Farmacia\Exceptions\BnafarException;
use App\Domain\Saude\Farmacia\Services\BnafarQueueService;
use App\Domain\Saude\Farmacia\Services\IntegracaoBnafarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarProtocoloBnafarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;

    private $service;

    private $protocolo;

    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * @var QueuedJob
     */
    private $queuedJob;

    /**
     * @param IntegracaoBnafarService $service
     * @param string $protocolo
     * @param QueueService $queueService
     * @param QueuedJob $queuedJob
     * @throws \Exception
     */
    public function __construct(
        IntegracaoBnafarService $service,
        $protocolo,
        QueueService $queueService,
        QueuedJob $queuedJob
    ) {
        if ($this->connection === 'sync' || env('QUEUE_DRIVER') === 'sync') {
            throw new \Exception('Não é possível executar esse serviço de forma síncrona. Contate o suporte.');
        }

        $this->service = $service;
        $this->protocolo = $protocolo;
        $this->queueService = $queueService;
        $this->queuedJob = $queuedJob;

        BnafarQueueService::newProtocolo($queueService->getBatch(), $protocolo);
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $this->service->processarProtocolo($this->protocolo);
            $inQueue = $this->queueService->terminate($this->queuedJob);
            if ($inQueue == 0) {
                BnafarQueueService::markAsDone($this->queueService->getBatch());
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 401 || $this->attempts() >= $this->tries) {
                throw $e;
            }
            $this->release($this->attempts() * 600);
        }
    }

    public function failed(\Exception $e)
    {
        $mensagem = $e->getMessage();
        if ($e instanceof BnafarException) {
            $mensagem = $e->getDetalhes();
        }
        $this->service->notificaUsuario('Erro ao processar protocolo no BNAFAR', $mensagem);
        $inQueue = $this->queueService->terminate($this->queuedJob);
        if ($inQueue == 0) {
            BnafarQueueService::markAsDone($this->queueService->getBatch());
        }

        BnafarQueueService::markAsFailed($this->queueService->getBatch(), $this->protocolo);
    }
}
