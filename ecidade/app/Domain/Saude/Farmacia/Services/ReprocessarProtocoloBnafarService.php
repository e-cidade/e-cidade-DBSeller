<?php

namespace App\Domain\Saude\Farmacia\Services;

use App\Domain\Saude\Farmacia\Clients\BnafarClient;
use App\Domain\Saude\Farmacia\Requests\ReprocessarProtocoloBnafarRequest;
use App\Jobs\Saude\Farmacia\ProcessarProtocoloBnafarJob;

class ReprocessarProtocoloBnafarService
{

    /**
     * @param ReprocessarProtocoloBnafarRequest $request
     * @return void
     * @throws \Exception
     */
    public function execute(ReprocessarProtocoloBnafarRequest $request)
    {
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);
        $client = new BnafarClient($unidade);
        $integracaoService = new IntegracaoBnafarService($client, $request->DB_id_usuario);

        $bnafarQueueService = new BnafarQueueService();
        $bnafarQueueService->newBatch($request->procedimento);
        $queueService = $bnafarQueueService->getQueueService($request->procedimento);
        $queuedJob = $queueService->next();

        dispatch(new ProcessarProtocoloBnafarJob($integracaoService, $request->protocolo, $queueService, $queuedJob));
    }
}
