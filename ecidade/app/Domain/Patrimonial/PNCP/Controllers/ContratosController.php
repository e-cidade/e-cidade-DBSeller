<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Requests\ExclusaoContratoRequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoContratoRequest;
use App\Domain\Patrimonial\PNCP\Requests\InclusaoDocumentoContratoRequest;
use App\Domain\Patrimonial\PNCP\Services\ContratoService;
use Exception;
use Illuminate\Http\Request;

class ContratosController
{
    /**
     * @param InclusaoContratoRequest $request
     * @param ContratoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function incluirContrato(InclusaoContratoRequest $request, ContratoService $service)
    {
        $response = $service->enviarContrato($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param InclusaoDocumentoContratoRequest $request
     * @param ContratoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function incluirDocumento(InclusaoDocumentoContratoRequest $request, ContratoService $service)
    {
        $response = $service->enviarDocumento($request);
        return new DBJsonResponse($response);
    }

    public function buscarContratos(Request $request, ContratoService $service)
    {
        $acordos = $service->buscarContratos($request);
        return new DBJsonResponse($acordos);
    }

    /**
     * @param ExclusaoContratoRequest $request
     * @param ContratoService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function excluirContrato(ExclusaoContratoRequest $request, ContratoService $service)
    {
        $service->excluirContrato($request);
        return new DBJsonResponse([], 'Contrato excluído com sucesso!');
    }

    /**
     * @param ExclusaoContratoRequest $request
     * @param ContratoService $service
     * @return void
     */
    public function excluirDocumento(Request $request, ContratoService $service)
    {
        $service->excluirDocumento($request);
    }

    /**
     * @param Request $request
     * @param ContratoService $service
     * @return DBJsonResponse
     */
    public function buscarDocumentos(Request $request, ContratoService $service)
    {
        $documentos = $service->buscarDocumentosContrato($request);
        return new DBJsonResponse($documentos);
    }
}
