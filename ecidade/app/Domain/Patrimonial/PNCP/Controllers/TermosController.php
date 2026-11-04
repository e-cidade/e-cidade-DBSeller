<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoDocumentoTermoRequest;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoTermoContratoRequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoDocumentoTermoRequest;
use App\Domain\Patrimonial\PNCP\Services\TermoService;
use Exception;
use Illuminate\Http\Request;

class TermosController
{
    /**
     * @param Request $request
     * @param TermoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function incluir(Request $request, TermoService $service)
    {
        $service->incluir($request);
        return new DBJsonResponse([]);
    }

    /**
     * @param Request $request
     * @param TermoService $service
     * @return DBJsonResponse
     */
    public function buscar(Request $request, TermoService $service)
    {
        $termos = $service->buscar($request);
        return new DBJsonResponse($termos);
    }

    /**
     * @param ExclusaoTermoContratoRequest $request
     * @param TermoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function retificar(Request $request, TermoService $service)
    {
        $service->retificar($request);
        return new DBJsonResponse([]);
    }

    /**
     * @param InclusaoDocumentoTermoRequest $request
     * @param TermoService $service
     * @return DBJsonResponse
     */
    public function incluirDocumento(InclusaoDocumentoTermoRequest $request, TermoService $service)
    {
        $service->incluirDocumento($request);
        return new DBJsonResponse([]);
    }

    /**
     * @param Request $request
     * @param TermoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarDocumentos(Request $request, TermoService $service)
    {
        $documentos = $service->buscarDocumentos($request);
        return new DBJsonResponse($documentos);
    }

    public function excluirDocumento(ExclusaoDocumentoTermoRequest $request, TermoService $service)
    {
        $service->excluirDocumento($request);
        return new DBJsonResponse([]);
    }

    /**
     * @param ExclusaoTermoContratoRequest $request
     * @param TermoService $service
     * @return DBJsonResponse
     */
    public function excluir(ExclusaoTermoContratoRequest $request, TermoService $service)
    {
        $service->excluir($request);
        return new DBJsonResponse([]);
    }
}
