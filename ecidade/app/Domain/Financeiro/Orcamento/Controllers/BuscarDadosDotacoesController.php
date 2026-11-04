<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Services\BuscarDadosDotacoesService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuscarDadosDotacoesController extends Controller
{

    public function buscarDadosFiltrosDotacoes(Request $request, BuscarDadosDotacoesService $service)
    {
        return new DBJsonResponse($service->buscarDadosFiltrosDotacoes($request));
    }

    public function getDotacoes($elemento, BuscarDadosDotacoesService $service)
    {
        return new DBJsonResponse($service->getDotacoes($elemento));
    }

    public function getSaldoDotacao($codigoDotacao, BuscarDadosDotacoesService $service)
    {
        return new DBJsonResponse($service->getSaldoDotacao($codigoDotacao));
    }

    public function getDesdobramentoMaterial($codigoMaterial, BuscarDadosDotacoesService $service)
    {
        return new DBJsonResponse($service->getDesdobramentoMaterial($codigoMaterial));
    }

    public function getDesdobramentoItem($codele, BuscarDadosDotacoesService $service)
    {
        return new DBJsonResponse($service->getDesdobramentoItem($codele));
    }
}
