<?php


namespace App\Domain\Financeiro\Planejamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\AtualizaPrevisaoExercicioRequest;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\CalculaProjecaoRequest;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\ValorBaseDespesaRequest;
use App\Domain\Financeiro\Planejamento\Services\ProjecaoDespesaService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

/**
 * Class ProjecaoDespesa
 * @package App\Domain\Financeiro\Planejamento\Controllers
 */
class ProjecaoDespesaController extends Controller
{
    public function buscar(CalculaProjecaoRequest $request, ProjecaoDespesaService $service)
    {
        $projecao = $service->porRequest($request)->get();
        return new DBJsonResponse($projecao, "Dados da projeção");
    }

    public function recalcular(CalculaProjecaoRequest $request, ProjecaoDespesaService $service)
    {
        $service->porRequest($request)
            ->setFormaRecalculo($request->get('formaRecalculo'))
            ->recalcular();

        return new DBJsonResponse([], "Recálculo executado sucesso.");
    }

    public function criarDetalhamentoNovasDotacoes(CalculaProjecaoRequest $request, ProjecaoDespesaService $service)
    {
        $service->porRequest($request)
            ->setFormaRecalculo($request->get('formaRecalculo'))
            ->criarDetalhamentoNovasDotacoes();
        return new DBJsonResponse([], "Criado dotações executado sucesso.");
    }

    /**
     * @param AtualizaPrevisaoExercicioRequest $request
     * @param ProjecaoDespesaService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function previsaoExercicio(AtualizaPrevisaoExercicioRequest $request, ProjecaoDespesaService $service)
    {
        $exercicio = (int)$request->get('exercicio');
        $service->atualizarPrevisao($request->get('id'), $exercicio, $request->get('valor'));
        return new DBJsonResponse([], "Valor atualizado");
    }

    /**
     * @param ValorBaseDespesaRequest $request
     * @return DBJsonResponse
     */
    public function atualizarValorBase(ValorBaseDespesaRequest $request)
    {
        $estimativa = DetalhamentoDespesa::find($request->get('id'));
        $estimativa->pl20_valorbase = $request->get('valor');
        $estimativa->save();
        return new DBJsonResponse([], "Valor atualizado");
    }
}
