<?php

namespace App\Domain\Saude\Farmacia\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Farmacia\Requests\ConsistirBnafarRequest;
use App\Domain\Saude\Farmacia\Requests\ExportarBnafarRequest;
use App\Domain\Saude\Farmacia\Requests\ValidarBnafarRequest;
use App\Domain\Saude\Farmacia\Services\CompetenciaBnafarService;
use App\Domain\Saude\Farmacia\Services\ConsistenciaBnafarService;
use App\Domain\Saude\Farmacia\Services\ExportarBnafarService;
use App\Http\Controllers\Controller;
use Exception;

class ExportacaoBnafarController extends Controller
{
    /**
     * @param ValidarBnafarRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function validar(ValidarBnafarRequest $request)
    {
        $periodo = [new \DateTime($request->periodoInicio), new \DateTime($request->periodoFim)];
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);

        return new DBJsonResponse(CompetenciaBnafarService::validar($unidade, $periodo));
    }

    /**
     * @param ConsistirBnafarRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function consistir(ConsistirBnafarRequest $request)
    {
        $periodo = [new \DateTime($request->periodoInicio), new \DateTime($request->periodoFim)];
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);

        return new DBJsonResponse(ConsistenciaBnafarService::consistir($periodo, $unidade, $request->procedimentos));
    }

    /**
     * @param ExportarBnafarRequest $request
     * @param ExportarBnafarService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function exportar(ExportarBnafarRequest $request, ExportarBnafarService $service)
    {
        $service->makeFromRequest($request);

        return new DBJsonResponse([], 'Processamento iniciado com sucesso.');
    }
}
