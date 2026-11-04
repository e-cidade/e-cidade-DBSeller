<?php

namespace App\Jobs\Saude\Farmacia;

use App\Domain\Core\Models\QueuedJob;
use App\Domain\Core\Services\QueueService;
use App\Domain\Saude\Farmacia\Exceptions\BnafarException;
use App\Domain\Saude\Farmacia\Services\BnafarEnviosService;
use App\Domain\Saude\Farmacia\Services\BnafarQueueService;
use App\Domain\Saude\Farmacia\Services\IntegracaoBnafarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportarBnafarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    /**
     * @var IntegracaoBnafarService
     */
    private $service;

    /**
     * @var string
     */
    private $procedimento;

    /**
     * @var array|object
     */
    private $dados;

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
     * @param string $procedimento
     * @param object $dados
     * @param QueueService $queueService
     * @throws \Exception
     */
    public function __construct(IntegracaoBnafarService $service, $procedimento, $dados, QueueService $queueService)
    {
        if ($this->connection === 'sync' || env('QUEUE_DRIVER') === 'sync') {
            throw new \Exception('Não é possível executar esse serviço de forma síncrona. Contate o suporte.');
        }

        $this->service = $service;
        $this->procedimento = $procedimento;
        $this->dados = $dados;
        $this->queueService = $queueService;
        $this->queuedJob = $queueService->next();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        try {
            $this->exportar();
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
        $this->service->notificaUsuario('Erro ao exportar movimentação.', $mensagem);
        $inQueue = $this->queueService->terminate($this->queuedJob);
        if ($inQueue == 0) {
            BnafarQueueService::markAsDone($this->queueService->getBatch());
        }
    }

    /**
     * @throws \Exception
     */
    private function exportar()
    {
        $response = $this->service->enviar($this->procedimento, $this->dados);

        if (!property_exists($response, 'protocolo')) {
            BnafarEnviosService::vincular($response->codigoBnafar, $this->dados->caracterizacao->codigoOrigem);
            $this->service->notificaUsuario(
                'Movimentação exportada para o sistema BNAFAR com sucesso.',
                'Foi concluída a exportação da movimentação.'
            );
            $this->queueService->terminate($this->queuedJob);
            BnafarQueueService::markAsDone($this->queueService->getBatch());
            return;
        }

        dispatch(
            new ProcessarProtocoloBnafarJob($this->service, $response->protocolo, $this->queueService, $this->queuedJob)
        );
    }
}
