<?php

namespace App\Domain\Financeiro\Planejamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Planejamento\Requests\Relatorios\CotasDespesaRequest;
use App\Domain\Financeiro\Planejamento\Requests\Relatorios\MetasArrecadacaoRequest;
use App\Domain\Financeiro\Planejamento\Requests\Relatorios\MetasVersusCotasRequest;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\CotasDespesaService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\MetasArrecadacaoPlanoPadraoService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\MetasArrecadacaoService;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\MetasVersusCotasService;
use App\Http\Controllers\Controller;

class RelatoriosCronogramaController extends Controller
{
    public function metaArrecadacao(MetasArrecadacaoRequest $request)
    {
        $service = new MetasArrecadacaoService($request->all());

        return new DBJsonResponse($service->emitir(), 'Metas de Arrecadação');
    }

    public function metaArrecadacaoPlanoPadrao(MetasArrecadacaoRequest $request)
    {
        $service = new MetasArrecadacaoPlanoPadraoService($request->all());

        return new DBJsonResponse($service->emitir(), 'Metas de Arrecadação');
    }

    public function cotaDespesa(CotasDespesaRequest $request)
    {
        $service = new CotasDespesaService($request->all());
        return new DBJsonResponse($service->emitir(), 'Cotas da Despesa');
    }

    public function metaVersusCota(MetasVersusCotasRequest $request)
    {
        $service = new MetasVersusCotasService($request->all());
        return new DBJsonResponse($service->emitir(), 'Metas de Arrecadação x Cotas da Despesa');
    }
}
