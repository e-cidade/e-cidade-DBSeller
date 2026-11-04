<?php

namespace App\Domain\Saude\Farmacia\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Farmacia\Requests\ConsultarProtocoloBnafarRequest;
use App\Domain\Saude\Farmacia\Requests\RelatorioProtocoloBnafarRequest;
use App\Domain\Saude\Farmacia\Requests\ReprocessarProtocoloBnafarRequest;
use App\Domain\Saude\Farmacia\Services\BnafarProtocoloService;
use App\Domain\Saude\Farmacia\Services\ReprocessarProtocoloBnafarService;
use App\Http\Controllers\Controller;

class ProtocoloBnafarController extends Controller
{
    /**
     * @param ConsultarProtocoloBnafarRequest $request
     * @param BnafarProtocoloService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function consultar(ConsultarProtocoloBnafarRequest $request, BnafarProtocoloService $service)
    {
        $competencia = \DBCompetencia::createFromString($request->competencia);
        $periodoInicio = new \DateTime("{$competencia->getAno()}-{$competencia->getMes()}-01");
        $periodo = [$periodoInicio, new \DateTime($periodoInicio->format('Y-m-t'))];
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);

        return new DBJsonResponse($service->consultar($unidade, $periodo, $request->pagina, $request->tamanho));
    }

    /**
     * @param RelatorioProtocoloBnafarRequest $request
     * @param BnafarProtocoloService $service
     * @return DBJsonResponse
     */
    public function relatorio(RelatorioProtocoloBnafarRequest $request, BnafarProtocoloService $service)
    {
        return new DBJsonResponse($service->gerarRelatorio($request));
    }

    /**
     * @param ReprocessarProtocoloBnafarRequest $request
     * @param ReprocessarProtocoloBnafarService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function reprocessar(ReprocessarProtocoloBnafarRequest $request, ReprocessarProtocoloBnafarService $service)
    {
        $service->execute($request);

        return new DBJsonResponse([], 'Processamento iniciado com sucesso.');
    }
}
