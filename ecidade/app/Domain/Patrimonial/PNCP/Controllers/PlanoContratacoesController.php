<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Enum\ElementosDespesaEnum;
use App\Domain\Patrimonial\PNCP\Models\PlanoContratacaoItem;
use App\Domain\Patrimonial\PNCP\Services\PlanoContratacoesService;
use Illuminate\Http\Request;

class PlanoContratacoesController
{
    /**
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     */
    public function buscarUnidades(PlanoContratacoesService $service)
    {
        $response = $service->buscarUnidades();
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function incluir(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->incluir($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     */
    public function elaboracao(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->elaboracao($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function incluirItem(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->incluirItem($request);
        return new DBJsonResponse($response);
    }


    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     */
    public function buscarItens(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->buscarItens($request);
        return new DBJsonResponse($response);
    }

    public function removerItem(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->removerItem($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws \ReflectionException
     */
    public function buscarElementoDespesa(Request $request)
    {
        $elementoDespesa = ElementosDespesaEnum::getElementoDespesa($request->categoriaItem);
        return new DBJsonResponse($elementoDespesa);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function editarItem(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->editarItem($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function buscarPlanos(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->buscarPlanos($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function editarPlano(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->editarPlano($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function excluirPlano(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->excluirPlano($request);
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @param PlanoContratacoesService $service
     * @return DBJsonResponse
     */
    public function relatorioItensPlano(Request $request, PlanoContratacoesService $service)
    {
        $response = $service->emitirRelatorio($request);
        return new DBJsonResponse($response->emitir(), 'Gerando relatório');
    }
}
