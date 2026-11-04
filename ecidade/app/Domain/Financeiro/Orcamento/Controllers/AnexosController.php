<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Services\Relatorios\AnexoDoisReceitaService;
use App\Domain\Financeiro\Orcamento\Services\Relatorios\AnexoTresService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnexosController extends Controller
{
    public function anexo3(Request $request)
    {
        $service = new AnexoTresService($request->all());
        return new DBJsonResponse($service->emitir(), 'Anexo 3 - Fontes da Receita');
    }

    public function receitaAnexo2(Request $request)
    {
        $service = new AnexoDoisReceitaService($request->all());
        return new DBJsonResponse($service->emitir(), 'Anexo 2 - Resumo da Receita');
    }
}
