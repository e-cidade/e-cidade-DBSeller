<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Models\Lancamento;
use App\Domain\Financeiro\Contabilidade\Models\Lote;
use App\Domain\Financeiro\Contabilidade\Resources\Lancamentos\ConsultaLancamentoManualResource;
use App\Domain\Financeiro\Contabilidade\Services\LancamentoManualService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NotaLancamentoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class LancamentoManualController extends Controller
{

    public function store(Request $request)
    {
        $service = new LancamentoManualService();
        $dados = $service->criarLancamentos($request->all());

        return new DBJsonResponse($dados, 'Lançamentos criados com sucesso.');
    }

    public function retificar(Request $request)
    {
        $service = new LancamentoManualService();
        $dados = $service->alterarLancamento($request->all());

        return new DBJsonResponse([], 'Lançamento alterado com sucesso.');
    }

    /**
     * @param Lancamento $lancamento
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function destroy(Lancamento $lancamento, Request $request)
    {
        $service = new LancamentoManualService();
        $service->excluirLancamento($lancamento);
        return new DBJsonResponse([], 'Lançamento excluído com sucesso.');
    }

    public function destroyLote(Lote $lote, Request $request)
    {
        $service = new LancamentoManualService();
        $service->excluirLote($lote);
        return new DBJsonResponse([], 'Lote de lançamentos excluído com sucesso.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function getLancamentosManuais(Request $request)
    {
        $filtros = $request->all();
        $service = new LancamentoManualService();
        $organizados = ConsultaLancamentoManualResource::organizaPorLote($service->getLancamentosManuais($filtros));
        return new DBJsonResponse(
            array_values($organizados),
            'Lançamentos encontrados.'
        );
    }

    public function proximoLote(Request $request)
    {
        $service = new LancamentoManualService();
        $proximoLote = $service->proximoLote($request->get('exercicio'), $request->get('instituicao'));

        return new DBJsonResponse($proximoLote, 'Número do próximo Lote');
    }

    public function notaLancamento(Request $request)
    {
        $service = new NotaLancamentoService();
        return new DBJsonResponse($service->emitirLote($request->get('idLote')), 'Nota de lançamento');
    }
}
