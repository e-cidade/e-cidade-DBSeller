<?php

namespace App\Domain\Tributario\Arrecadacao\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Services\ProcessarBaixaService;
use App\Http\Controllers\Controller;
use ECidade\Tributario\Caixa\Service\CancDebitosService;
use Illuminate\Http\Request;

class ProcessarBaixaController extends Controller
{

    public function __construct(ProcessarBaixaService $service)
    {
        $this->service = $service;
    }

    public function buscadados(Request $request)
    {
        $buscaDados = $this->service->buscadados($request);
        return new DBJsonResponse($buscaDados, "");
    }

    public function processar(Request $request)
    {
        $processar = new CancDebitosService();
        $processar->salvar($request);
        return new DBJsonResponse(
            $processar,
            "Existem outros débitos de fases que não sejam do seu departamento e que não serão baixados."
        );
    }
}
