<?php

namespace App\Domain\Financeiro\Planejamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\AcompanhamentoLdoService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\AcompanhamentoPpaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RelatorioAcompanhamentoController extends Controller
{

    public function emitir(Request $request)
    {
        $service = new AcompanhamentoLdoService();
        if ($request->get('tipo') === 'PPA') {
            $service = new AcompanhamentoPpaService();
        }

        $service->setFiltros($request->all());


        return new DBJsonResponse($service->emitir(), 'Acompanhamento Planejamento');
    }
}
