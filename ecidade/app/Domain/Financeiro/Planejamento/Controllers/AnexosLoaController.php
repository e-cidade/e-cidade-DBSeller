<?php

namespace App\Domain\Financeiro\Planejamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\AnexoDoisReceitaService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\AnexoTresReceitaService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\EstimativaReceitaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnexosLoaController extends Controller
{
    public function orcamento(Request $request)
    {
        $service = new EstimativaReceitaService($request->all());
        return new DBJsonResponse($service->emitirPdf(), 'Previsão Orçamento');
    }

    public function anexoDois(Request $request)
    {
        $service = new AnexoDoisReceitaService($request->all());
        return new DBJsonResponse($service->emitirPdf(), 'Anexo 2 - Resumo da Receita');
    }

    public function anexoTres(Request $request)
    {
        $service = new AnexoTresReceitaService($request->all());
        return new DBJsonResponse($service->emitirPdf(), 'Anexo 3 - Fontes das Receitas');
    }
}
