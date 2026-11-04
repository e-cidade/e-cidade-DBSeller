<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaComplementoService;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use App\Domain\Financeiro\Orcamento\Models\Receita;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ManutencaoPlanoOrcamentarioReceitaService extends ManutencaoPlanoOrcamentarioService
{
    /**
     * @param string $estrutural
     * @param int $exercicio
     * @return Collection
     */
    public function buscarReceitasParaExcluisao($estrutural, $exercicio)
    {
        $receitas = $this->getContas(['estrutural' => $estrutural, "exercicio" => $exercicio]);
        $balancete = $this->executaBalanceteReceita($estrutural, $exercicio);

        // filtrei as receitas sem execução orçamentaria
        $receitasExcluir = $receitas->reject(function (ConplanoOrcamento $plano) use ($balancete) {
            $dadoBalancete = collect($balancete)->filter(function ($conta) use ($plano) {
                return $plano->c60_estrut === $conta->natureza;
            })->shift();

            if (!is_null($dadoBalancete)) {
                $temSaldo = ($dadoBalancete->previsao_atualizada != 0 ||
                    $dadoBalancete->arrecadado_periodo != 0 ||
                    $dadoBalancete->arrecadado_acumulado != 0
                );
                return $temSaldo;
            }
        });

        // filtra as receitas que possuíram lançamentos contábeis no exercício
        $fontesComLancamento = $this->getReceitasComLancamentos($estrutural, $exercicio)->toArray();
        $receitasExcluir = $receitasExcluir->reject(function (ConplanoOrcamento $plano) use ($fontesComLancamento) {
            return in_array($plano->c60_estrut, $fontesComLancamento);
        });

        $fontesComEstimativa = EstimativaReceita::query()
            ->where('anoorcamento', $exercicio)
            ->get()
            ->filter(function (EstimativaReceita $estimativaReceita) {
                //
                $valores = $estimativaReceita->getValores()->filter(function (Valor $valor) {
                    return $valor->pl10_valor > 0;
                });

                return !$valores->isEmpty();
            })
            ->map(function (EstimativaReceita $estimativaReceita) {
                return $estimativaReceita->getNaturezaOrcamento()->o57_fonte;
            })
            ->toArray();

        // filtra se a receita foi usada no planejamento
        $receitasExcluir = $receitasExcluir->reject(function (ConplanoOrcamento $plano) use ($fontesComEstimativa) {
            return in_array($plano->c60_estrut, $fontesComEstimativa);
        });

        return $receitasExcluir;
    }

    /**
     * @param array $contas
     */
    protected function removerVinculos(array $contas)
    {
        $this->removeVinculosPlanejamento($contas);
        $this->removeVinculosTesouraria($contas);
        $this->removeVinculosOrcamento($contas);
        $this->removeVinculosContabilidade($contas);
    }

    private function executaBalanceteReceita($estrutural, $exercicio)
    {
        $filtros = [
            "natureza" => $estrutural,
            "dataInicio" => sprintf('01/01/%s', $exercicio),
            "dataFinal" => sprintf('31/12/%s', $exercicio),
            "nivelAgrupar" => 0,
            "apenasComMovimentacao" => 0
        ];

        $service = new BalanceteReceitaComplementoService();
        $service->setFiltrosRequest($filtros);

        $service->setInstituicoes(DBConfig::select(['codigo', 'nomeinst'])->get());
        return $service->getArvore();
    }

    protected function removeVinculosOrcamento(array $codigosContas)
    {
        parent::removeVinculosOrcamento($codigosContas);

        // deletar receita
        DB::table('orcamento.orcreceita')
            ->where('o70_anousu', '>=', $this->exercicio)
            ->whereIn('o70_codfon', $codigosContas)
            ->delete();

        DB::table('orcamento.orcfontes')
            ->where('o57_anousu', '>=', $this->exercicio)
            ->whereIn('o57_codfon', $codigosContas)
            ->delete();
    }

    protected function removeVinculosTesouraria(array $codigosContas)
    {
        $codigosReceitas = Receita::query()
            ->where('o70_anousu', '>=', $this->exercicio)
            ->whereIn('o70_codfon', $codigosContas)
            ->get()
            ->map(function ($receita) {
                return $receita->o70_codrec;
            })
            ->toArray();

        if (!empty($codigosReceitas)) {
            DB::table('caixa.taborc')
                ->where('k02_anousu', $this->exercicio)
                ->whereIn('k02_codrec', $codigosReceitas)
                ->delete();
        }
    }

    protected function removeVinculosPlanejamento(array $contas)
    {
        DB::table('planejamento.fatorcorrecaoreceita')
            ->where('anoorcamento', '>=', $this->exercicio)
            ->whereIn('orcfontes_id', $contas)
            ->delete();

        DB::table('planejamento.estimativareceita')
            ->where('anoorcamento', '>=', $this->exercicio)
            ->whereIn('orcfontes_id', $contas)
            ->delete();

        EstimativaReceita::query()
            ->where('anoorcamento', '>=', $this->exercicio)
            ->whereIn('orcfontes_id', $contas)
            ->delete();
    }

    private function getReceitasComLancamentos($estrutural, $exercicio)
    {
        return FonteReceita::query()
            ->distinct()
            ->select('o57_fonte')
            ->whereRaw("o57_fonte like '$estrutural%'")
            ->where('o57_anousu', $exercicio)
            ->join('orcreceita', function ($join) {
                $join->on('orcreceita.o70_anousu', '=', 'orcfontes.o57_anousu')
                    ->on('orcreceita.o70_codfon', '=', 'orcfontes.o57_codfon');
            })
            ->join('conlancamrec', function ($join) {
                $join->on('conlancamrec.c74_anousu', '=', 'orcreceita.o70_anousu')
                    ->on('conlancamrec.c74_codrec', '=', 'orcreceita.o70_codrec');
            })
            ->get()
            ->map(function ($fonte) {
                return $fonte->o57_fonte;
            });
    }
}
