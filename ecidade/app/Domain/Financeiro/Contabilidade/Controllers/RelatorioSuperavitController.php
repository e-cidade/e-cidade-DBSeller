<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\Relatorios\RelatorioSuperavitRequest;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\SuperavitFinanceiroPorRecursoService;
use App\Http\Controllers\Controller;

class RelatorioSuperavitController extends Controller
{
    public function viewPorRecurso()
    {
        return view(
            'financeiro.contabilidade.relatorios.conferencia.superavit-por-recurso',
            [
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu'),
                "dataSistema" => date('Y-m-d', session('DB_datausu'))
            ]
        );
    }

    /**
     * Retorna o processamento do Superávit Financeiro
     * @param RelatorioSuperavitRequest $request
     * @return DBJsonResponse
     */
    public function porRecurso(RelatorioSuperavitRequest $request)
    {
        $service = new SuperavitFinanceiroPorRecursoService();
        $service->setFiltros($request->all());
        return new DBJsonResponse($service->emitir(), 'Superávit Financeiro por Recurso');
    }
}
