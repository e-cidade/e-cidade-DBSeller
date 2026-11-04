<?php

namespace App\Jobs\Patrimonial\Material;

use App\Domain\Patrimonial\Material\Services\ProcessarLancamentoContabilRequisicaoService;
use App\Domain\Patrimonial\Material\Services\ProcessarLancamentoQueueService;
use BusinessException;
use ECidade\Lib\Session\DatabaseSession;
use ECidade\Lib\Session\DefaultSession;
use EventoContabil;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LancamentoAuxiliarMovimentacaoEstoque;
use SingletonRegraDocumentoContabil;

class ProcessarLancamentoContabilRequisicaoJob implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;

    private $queueService;

    private $queuedJob;
    /**
     * @var ProcessarLancamentoContabilRequisicaoService
     */
    private $service;

    public function __construct(ProcessarLancamentoContabilRequisicaoService $service)
    {


        $this->service = $service;
        // cria um batch/lote de jobs, que nesse caso eh somente 1
        $this->queueService = ProcessarLancamentoQueueService::newBatch($service);

        // cadastra o job na lista de controle, para poder contabilizar posteriormente
        $this->queuedJob = $this->queueService->next();
    }

    public function handle()
    {
        try {
            $this->service->processar();

            //informa que o processamento foi realizado com sucesso
            $this->queueService->markAsSuccess();

            // remove o job da lista de controle de jobs
            $this->queueService->terminate($this->queuedJob);
        } catch (\Exception $e) {
            // pode utilizar de outra forma, simplesmente sem fazer try catch
            // assim ele tenta toda hora, até atingir o limite definido em $tries
            if ($this->attempts() >= $this->tries) {
                throw $e;
            }

            // tenta denovo depois de um tempo
            // numero da tentativa * 10 minutos
            $this->release($this->attempts() * 600);
        }
    }

    public function failed(\Exception $e)
    {
        // remove o job da lista de controle de jobs
        $this->queueService->terminate($this->queuedJob);

        // pode notificar o usuário ou qualquer outro tipo de notificação
        $this->queueService->markAsFailed($e);
    }
}
