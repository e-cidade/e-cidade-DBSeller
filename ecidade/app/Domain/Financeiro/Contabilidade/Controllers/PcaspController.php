<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Contabilidade\Models\Sistema;
use App\Domain\Financeiro\Contabilidade\Models\SistemaConta;
use App\Domain\Financeiro\Contabilidade\Requests\ExcluirReduzidoRequest;
use App\Domain\Financeiro\Contabilidade\Requests\PcaspEcidadeRequest;
use App\Domain\Financeiro\Contabilidade\Requests\PcaspPadraoRequest;
use App\Domain\Financeiro\Contabilidade\Services\ManutencaoPlanoContasService;
use App\Domain\Financeiro\Contabilidade\Services\PcaspEcidadeService;
use App\Domain\Financeiro\Contabilidade\Services\PcaspService;
use App\Domain\Financeiro\Contabilidade\Services\VincularContasPcaspService;
use App\Domain\Financeiro\Contabilidade\Services\VincularPcaspService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class PcaspController extends Controller
{
    public function sistemas()
    {
        $sistemas = Sistema::query()->orderBy('c52_codsis')->get();
        return new DBJsonResponse($sistemas, 'Sistemas de Identificações das Contas');
    }

    public function sistemaConta()
    {
        $sistemas = SistemaConta::query()->orderBy('c65_sequencial')->get();
        return new DBJsonResponse($sistemas, 'Sistema de contas');
    }

    public function getContasPadrao(PcaspPadraoRequest $request)
    {
        $service = new VincularPcaspService();
        return new DBJsonResponse($service->getContasPadrao($request->all()), 'Contas encontradas');
    }

    public function getContasEcidade(PcaspEcidadeRequest $request)
    {
        $service = new PcaspService();
        return new DBJsonResponse($service->getContasEcidade($request->all()), 'Contas encontradas');
    }

    public function salvarContaCaixa(Request $request)
    {
        $service = new PcaspService();
        return new DBJsonResponse($service->salvarContaCaixa($request->all()), 'Conta salva com sucesso.');
    }

    public function salvarContaBancaria(Request $request)
    {
        $service = new PcaspService();
        return new DBJsonResponse($service->salvarContaBancaria($request->all()), 'Conta salva com sucesso.');
    }

    public function salvarContaExtra(Request $request)
    {
        $service = new PcaspService();
        $conplano = $service->salvarContaExtraOrcamentaria($request->all());
        return new DBJsonResponse($conplano, 'Conta salva com sucesso.');
    }

    public function salvarOutrasContas(Request $request)
    {
        $service = new PcaspService();
        return new DBJsonResponse($service->salvarOutrasContas($request->all()), 'Conta salva com sucesso.');
    }

    public function vincularGeral(Request $request)
    {
        $service = new VincularPcaspService();
        $service->procesarVinculoGeral($request->all());
        return new DBJsonResponse([], 'Contas vinculadas com sucesso.');
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
        $service = new VincularPcaspService();
        $service->vincular($request->get('pcasp_id'), $idsContasEcidade);
        return new DBJsonResponse([], 'Contas vinculadas com sucesso.');
    }

    public function estruturalEstrutural($estrutural, $exercicio)
    {
        $conta = Conplano::query()
            ->where('c60_estrut', $estrutural)
            ->where('c60_anousu', $exercicio)
            ->first();
        $existe = !is_null($conta);
        return new DBJsonResponse($existe, '');
    }

    public function editarEstruturais(Request $request)
    {
        $service = new PcaspService();
        $service->editarEstruturais($request->all());
        return new DBJsonResponse([], 'Edições das contas concluídas.');
    }

    /**
     * @todo  verificar com Leandro que tabelas sem FK como contabilidade.contranslr o que fazer
     *
     *
     */
    public function removerReduzido(ExcluirReduzidoRequest $request)
    {
        $service = new PcaspService();
        $service->removerReduzido($request->get('reduzido'), $request->get('exercicio'));

        $conta = Conplano::query()
            ->where('c60_codcon', $request->get('codcon'))
            ->where('c60_anousu', $request->get('exercicio'))
            ->first();

        return new DBJsonResponse(['excluiuConta' => is_null($conta)], 'Reduzido removido com sucesso.');
    }

    public function contasSemUso($estrutural, $exercicio)
    {
        $service = new ManutencaoPlanoContasService();
        return new DBJsonResponse($service->getContasSemUso($estrutural, $exercicio), 'Contas');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function exclusaoGeral(Request $request)
    {
        $contas = \JSON::create()->parse(str_replace('\"', '"', $request->contas));

        $service = new ManutencaoPlanoContasService();
        $service->excluirContas($contas);

        return new DBJsonResponse([], 'Contas excluídas com sucesso.');
    }


    public function mapeamentosRealizados($tipo, $exercicio)
    {
        $tipo = $tipo === 'uniao';

        $map = (object)[
            'pcasp' => false,
            'despesa' => false,
            'receita' => false,
        ];

        $map->pcasp = Pcasp::query()
                ->where('uniao', $tipo)
                ->where('exercicio', $exercicio)
                ->whereRaw('exists(select 1 from pcaspconplano where pcasp_id = pcasp.id)')
                ->get()
                ->count() > 0;

        $map->despesa = PlanoDespesa::query()
                ->where('uniao', $tipo)
                ->where('exercicio', $exercicio)
                ->whereRaw('
                exists(select 1 from planodespesaconplanoorcamento where planodespesa_id = planodespesa.id)
                ')
                ->get()
                ->count() > 0;


        $map->receita = PlanoReceita::query()
                ->where('uniao', $tipo)
                ->where('exercicio', $exercicio)
                ->whereRaw('
                exists(select 1 from planoreceitaconplanoorcamento where planoreceita_id = planoreceita.id)
                ')
                ->get()
                ->count() > 0;

        return new DBJsonResponse($map, '');
    }

    public function importarVinculo(Request $request)
    {
        $service = new VincularPcaspService();
        $service->importarVinculo($request->all());
        return new DBJsonResponse([], 'Mapeamento executado com base no exercício anterior');
    }
}
