<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Services\AtaRegistroPrecoService;
use Illuminate\Http\Request;

class AtaRegistroPrecoController
{
    /**
     * @param Request $request
     * @param AtaRegistroPrecoService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function incluir(Request $request, AtaRegistroPrecoService $service)
    {
        $response = $service->incluir($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param AtaRegistroPrecoService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function buscar(Request $request, AtaRegistroPrecoService $service)
    {
        $response = $service->buscar($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param AtaRegistroPrecoService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function excluir(Request $request, AtaRegistroPrecoService $service)
    {
        $response = $service->excluir($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param AtaRegistroPrecoService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function retificar(Request $request, AtaRegistroPrecoService $service)
    {
        $response = $service->retificar($request);
        return new DBJsonResponse($response);
    }
}
