<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Clients\BnafarClient;
use App\Domain\Saude\Farmacia\Factories\IntegracaoBnafarFactory;
use App\Domain\Saude\Farmacia\Requests\ExportarBnafarRequest;
use App\Jobs\Saude\Farmacia\ExportarLoteBnafarJob;

class ExportarBnafarService
{
    /**
     * @param ExportarBnafarRequest $request
     * @return void
     * @throws \Exception
     */
    public function makeFromRequest(ExportarBnafarRequest $request)
    {
        $data = $request->has('periodoInicio') ? new \DateTime($request->periodoInicio) : new \DateTime();
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);
        $client = new BnafarClient($unidade);
        $integracaoService = new IntegracaoBnafarService($client, $request->DB_id_usuario);
        $service = new ProcedimentosBnafarService($integracaoService);

        if ($request->has('procedimentos')) {
            $periodoFim = new \DateTime($request->periodoFim);
            $service->setPeriodo($data, $periodoFim);
            $this->exportarLote($service, $request->procedimentos, $unidade, $request->DB_id_usuario);
        } else {
            $this->exportar($service, $request->codigoMovimentacao, $request->procedimento, $unidade);
        }
    }

    /**
     * @param ProcedimentosBnafarService $service
     * @param array $procedimentos
     * @param \UnidadeProntoSocorro $unidade
     * @param integer $usuario
     * @return void
     * @throws \Exception
     */
    private function exportarLote($service, $procedimentos, $unidade, $usuario)
    {
        $bnafarQueueService = new BnafarQueueService();
        foreach ($procedimentos as $procedimento) {
            $strategy = IntegracaoBnafarFactory::getStrategy($procedimento, $unidade);
            $service->addStrategy($strategy);
            $bnafarQueueService->newBatch($procedimento);
        }

        dispatch(new ExportarLoteBnafarJob($service, $usuario, $bnafarQueueService));
    }

    /**
     * @param ProcedimentosBnafarService $service
     * @param integer $codigoMovimentacao
     * @param integer $procedimento
     * @param \UnidadeProntoSocorro $unidade
     * @return void
     * @throws \Exception
     */
    private function exportar($service, $codigoMovimentacao, $procedimento, $unidade)
    {
        $strategy = IntegracaoBnafarFactory::getStrategy($procedimento, $unidade);
        $strategy->setCodigoMovimentacao($codigoMovimentacao);
        $bnafarQueueService = new BnafarQueueService();
        $bnafarQueueService->newBatch($procedimento);

        $service->processar($strategy, $bnafarQueueService->getQueueService($procedimento));
    }
}
