<?php

namespace App\Domain\Saude\Farmacia\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Farmacia\Services\ConsistenciaBnafarService;
use App\Domain\Saude\Farmacia\Services\InconsistenciasBnafarService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InconsistenciasBnafarController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function get(Request $request)
    {
        $competencia = \DBCompetencia::createFromString($request->competencia);
        $unidade = \UnidadeProntoSocorroRepository::getUnidadeProntoSocorroByCodigo($request->DB_coddepto);

        return new DBJsonResponse(
            ConsistenciaBnafarService::getInconsistencias($competencia, $unidade),
            'Inconsistências encontradas.'
        );
    }

    /**
     * @param Request $request
     * @param InconsistenciasBnafarService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function salvarMovimentacao(Request $request, InconsistenciasBnafarService $service)
    {
        $service->salvarMovimentacao((object)$request->all());
        return new DBJsonResponse(
            [],
            'Movimentação salva com sucesso.'
        );
    }
}
