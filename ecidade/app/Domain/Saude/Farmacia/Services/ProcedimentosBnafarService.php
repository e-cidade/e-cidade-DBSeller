<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Core\Services\QueueService;
use App\Domain\Saude\Farmacia\Contracts\ProcedimentoBnafar;
use App\Jobs\Saude\Farmacia\ExportarBnafarJob;
use Exception;

class ProcedimentosBnafarService
{
    /**
     * @var IntegracaoBnafarService
     */
    private $integracaoService;

    /**
     * @var ProcedimentoBnafar[]
     */
    private $strategies;

    /**
     * @var array
     */
    private $periodo = [];

    /**
     * @param IntegracaoBnafarService $integracaoService
     */
    public function __construct(IntegracaoBnafarService $integracaoService)
    {
        $this->integracaoService = $integracaoService;
    }

    /**
     * @param ProcedimentoBnafar $strategy
     */
    public function addStrategy(ProcedimentoBnafar $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param \DateTime $periodoInicio
     * @param \DateTime $periodoFim
     * @return ProcedimentosBnafarService
     */
    public function setPeriodo(\DateTime $periodoInicio, \DateTime $periodoFim)
    {
        $this->periodo = [$periodoInicio, $periodoFim];
        return $this;
    }

    /**
     * Processa e envia o arquivo informado
     * @param ProcedimentoBnafar $strategy
     * @param QueueService $queueService
     */
    public function processar(ProcedimentoBnafar $strategy, QueueService $queueService)
    {
        $dados = $strategy->processar();
        $this->enviar($strategy->getProcedimento(), $dados, $queueService);
    }

    /**
     * Processa e envia os lotes dos arquivos informados
     * @throws Exception
     */
    public function processarLotes(BnafarQueueService $bnafarQueueService)
    {
        foreach ($this->strategies as $strategy) {
            foreach ($strategy->processarLote($this->periodo) as $lote) {
                $service = $bnafarQueueService->getQueueService($strategy->getTipo());
                $this->enviar($strategy->getProcedimentoLote(), $lote, $service);
            }
        }
    }

    /**
     * @param string $procedimento
     * @param array|object $dados
     */
    private function enviar($procedimento, $dados, QueueService $queueService)
    {
        dispatch(new ExportarBnafarJob($this->integracaoService, $procedimento, $dados, $queueService));
    }
}
