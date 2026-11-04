<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Requests\UnidadeRequest;
use App\Domain\Patrimonial\PNCP\Services\UnidadeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnidadesController extends Controller
{
    /**
     * @param UnidadeRequest $request
     * @param UnidadeService $service
     * @return DBJsonResponse
     */
    public function incluir(UnidadeRequest $request, UnidadeService $service)
    {
        $codigoIbge = $request->codigoIbge;
        $unidadeCodigo = $request->unidadeCodigo;
        $unidadeNome = stripslashes($request->unidadeNome);
        $documento = $request->documento;
        $ativo = $request->ativo;
        $instit = $request->DB_instit;
        $alteracao = $request->alteracao;

        $response = $service->incluirUnidadeApi(
            $documento,
            $codigoIbge,
            $unidadeCodigo,
            stripslashes(utf8_encode($unidadeNome)),
            $ativo,
            $instit,
            $alteracao
        );
        return new DBJsonResponse([], $response);
    }

    /**
     * @param Request $request
     * @param UnidadeService $service
     * @return DBJsonResponse
     */
    public function buscarEntidade(Request $request, UnidadeService $service)
    {
        $response = $service->buscaEntidade($request->get('documento'));
        return new DBJsonResponse($response, '');
    }

    public function buscarUnidades(Request $request, UnidadeService $service)
    {
        $response = $service->buscarUnidades($request->get('documento'));
        return new DBJsonResponse($response, '');
    }

    public function buscarUnidadesAtivas(Request $request, UnidadeService $service)
    {
        $response = $service->buscarUnidaedsAtivas($request->get('DB_instit'));
        return new DBJsonResponse($response, '');
    }
}
