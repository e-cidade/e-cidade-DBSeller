<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Services\ManutencaoPlanoOrcamentarioDespesaService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPlanoDespesaService;
use App\Domain\Financeiro\Contabilidade\Services\VincularPlanoOrcamentarioDespesaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanoOrcamentarioDespesaController extends Controller
{
    /**
     * @param $estrutural
     * @param $exercicio
     * @return DBJsonResponse
     */
    public function getDespesasSemUso($estrutural, $exercicio)
    {
        $service = new ManutencaoPlanoOrcamentarioDespesaService();
        $receitasExcluir = $service->buscarDespesasParaExclusao($estrutural, $exercicio);
        return new DBJsonResponse(array_values($receitasExcluir->toArray()), 'Receitas a serem excluídas.');
    }

    /**
     * @throws \Exception
     */
    public function exclusaoGeral(Request $request)
    {
        $contas = \JSON::create()->parse(str_replace('\"', '"', $request->contas));
        $service = new ManutencaoPlanoOrcamentarioDespesaService();
        $service->excluirContas($contas);

        $retorno = [];
        $msgLog = $service->getLog();

        if (!empty($msgLog)) {
            $retorno = ['logs' => $msgLog];
        }
        return new DBJsonResponse($retorno, 'Contas excluídas com sucesso.');
    }

    public function getContasPadrao(Request $request)
    {
        $service = new VincularPlanoOrcamentarioDespesaService();
        $contas = $service->getContasPadrao($request->all());

        return new DBJsonResponse($contas, 'Contas encontradas.');
    }

    /**
     * Retorna as contas do e-cidade e se a conta esta vínculada a conta do plano de governo
     * @param Request $request
     * @return DBJsonResponse
     */
    public function getContasMapearEcidade(Request $request)
    {
        $service = new VincularPlanoOrcamentarioDespesaService();
        $contas = $service->getContasEcidade($request->all());

        return new DBJsonResponse(array_values($contas), 'Contas encontradas.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function vincular(Request $request)
    {
        $idsContasEcidade = [];
        if ($request->has('contas_ecidade')) {
            $idsContasEcidade = $request->get('contas_ecidade');
        }
        $service = new VincularPlanoOrcamentarioDespesaService();
        $planoOrcamentario = $service->getPlanoOrcamentario($request->get('planoorcamentario_id'));
        $service->vincular($planoOrcamentario, $idsContasEcidade);
        return new DBJsonResponse([], 'Contas vinculadas com sucesso.');
    }

    public function vinculoGeral(Request $request)
    {
        $service = new VincularPlanoOrcamentarioDespesaService();
        $service->vinculoGeral($request->all());
        return new DBJsonResponse([], 'Contas vinculadas com sucesso.');
    }


    public function desvincular(Request $request)
    {
        $service = new VincularPlanoOrcamentarioDespesaService();
        $codigo = $request->get('conplanoorcamento_codigo');
        $service->desvincular($codigo, $request->get('tipoPlano'), PlanoContas::ORIGEM_DESPESA);
        return new DBJsonResponse([], 'Contas vinculadas com sucesso.');
    }

    /**
     * @param Request $request
     * @return void
     */
    public function mapeamento(Request $request)
    {
        $service = new MapeamentoPlanoDespesaService();
        $service->filtros($request->all());
        return new DBJsonResponse($service->emitir(), 'Mapeamento das contas vinculadas.');
    }

    public function importarVinculo(Request $request)
    {
        $service = new VincularPlanoOrcamentarioDespesaService();
        $service->importarVinculo($request->all());
        return new DBJsonResponse([], 'Mapeamento executado com base no exercício anterior');
    }
}
