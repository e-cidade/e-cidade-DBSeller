<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigeConlancamRecurso;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigeLancamentosEstoquePatrimonio;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigeLancamentoSlips;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigirLancamentosDocumento142;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigirLancamentosEmpenhoService;
use App\Domain\Financeiro\Contabilidade\Services\Fix\CorrigirLancamentosSuplementacoes;
use App\Domain\Financeiro\Contabilidade\Services\Fix\GerarCsvAjusteSaldo;
use App\Domain\Financeiro\Contabilidade\Services\Fix\LancamentosSemRecursoService;
use App\Domain\Financeiro\Contabilidade\Services\Fix\LoteLancamentoManualService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManutencaoLancamentosController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function criarRecursos(Request $request)
    {
        $service = new LancamentosSemRecursoService();
        $service->arrumar($request->get('exercicio'));

        return new DBJsonResponse([], 'Executado com sucesso');
    }

    public function corrigirRecursosEmpenho(Request $request)
    {
        $service = new CorrigirLancamentosEmpenhoService();
        $service->arrumar($request->get('exercicio'));
        return new DBJsonResponse([], 'Executado com sucesso');
    }

    public function corrigirDocumento142(Request $request)
    {
        $service = new CorrigirLancamentosDocumento142();
        $service->arrumar($request->get('exercicio'));
        return new DBJsonResponse([], 'Executado com sucesso');
    }

    public function corrigirSuplementacoes(Request $request)
    {
        $service = new CorrigirLancamentosSuplementacoes();
        $service->arrumar($request->get('exercicio'));
        return new DBJsonResponse([], 'Executado com sucesso');
    }

    public function corrigirConlancamrecurso(Request $request)
    {
        $service = new CorrigeConlancamRecurso();
        $service->arrumar($request->get('exercicio'), $request->get('data'));
        return new DBJsonResponse([], 'Executado com sucesso');
    }

    /**
     * Corrige os lançamentos dos documentos: 120, 121, 130, 131, 140, 141, 150, 151, 152, 153, 160, 161, 162, 163
     * @param Request $request
     * @return DBJsonResponse
     */
    public function corrigirSlips(Request $request)
    {
        $service = new CorrigeLancamentoSlips();
        $service->arrumar($request->get('exercicio'));
        return new DBJsonResponse([], 'Executado com sucesso');
    }

    public function ajusteSaldoContaMsc(Request $request)
    {
        $service = new LoteLancamentoManualService();
        $service->processarCsv($request->importar);
        return new DBJsonResponse([], 'Lancamentos executados com sucesso.');
    }

    public function corrigirEstoquePatrimonio(Request $request)
    {
        $service = new CorrigeLancamentosEstoquePatrimonio();
        $service->arrumar($request->get('data'));
        return new DBJsonResponse([], 'Lancamentos executados com sucesso.');
    }

    public function geraCsvSaldoContaMsc(Request $request)
    {
        $service = new GerarCsvAjusteSaldo($request->all());
        return new DBJsonResponse(['csv' => $service->gerar()], 'Csv.');
    }
}
