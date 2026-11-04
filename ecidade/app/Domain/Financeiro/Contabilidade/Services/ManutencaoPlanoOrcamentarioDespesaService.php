<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\RelatorioBalanceteDespesaService;
use App\Domain\Financeiro\Orcamento\Models\Dotacao;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use JSON;

class ManutencaoPlanoOrcamentarioDespesaService extends ManutencaoPlanoOrcamentarioService
{
    /**
     * Valida as contas orçamentárias da despesa que não possuem uso no exercício atual
     * @param string $estrutural
     * @param int $exercicio
     * @return Collection
     */
    public function buscarDespesasParaExclusao($estrutural, $exercicio)
    {
        $contas = $this->getContas(['estrutural' => $estrutural, "exercicio" => $exercicio]);
        $estruturaisComMovimento = $this->estruturaisComMovimento($exercicio);

        // exclui da lista contas que não tem o sexto nível
        $despesasExcluir = $contas->reject(function (ConplanoOrcamento $conta) {
            $estrutural = new Estrutural($conta->c60_estrut);
            return $estrutural->getNivel() !== 6;
        });

        // remove da lista contas com previsão de despesa
        $despesasExcluir = $despesasExcluir->reject(function (ConplanoOrcamento $conta) use ($estruturaisComMovimento) {
            return $this->temMovimentacao($estruturaisComMovimento, $conta);
        });

        $elementosComEstimativa = DetalhamentoDespesa::query()
            ->where('pl20_anoorcamento', $exercicio)
            ->get()
            ->filter(function (DetalhamentoDespesa $detalhamentoDespesa) {
                $valores = $detalhamentoDespesa->getValores()->filter(function (Valor $valor) {
                    return $valor->pl10_valor > 0;
                });

                return !$valores->isEmpty();
            })->map(function (DetalhamentoDespesa $detalhamentoDespesa) {
                return $detalhamentoDespesa->getNaturezaDespesa()->o56_elemento;
            })
            ->toArray();

        // filtra se a receita foi usada no planejamento
        $despesasExcluir = $despesasExcluir->reject(function (ConplanoOrcamento $plano) use ($elementosComEstimativa) {
            return in_array($plano->c60_estrut, $elementosComEstimativa);
        });

        return $despesasExcluir;
    }

    /**
     * @param $exercicio
     * @return array
     */
    public function estruturaisComMovimento($exercicio)
    {
        return DB::select("
          select c60_estrut as estrutural
            from (
            select c60_estrut
              from conlancam
              join conlancamele on c67_codlan = c70_codlan
              join conplanoorcamento on conplanoorcamento.c60_anousu = c70_anousu
                   and conplanoorcamento.c60_codcon = c67_codele
             where c70_anousu = $exercicio
            union
            select c60_estrut
              from conlancam
              join conlancamdot on c73_anousu = c70_anousu
                   and c73_codlan = c70_codlan
              join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
              join conplanoorcamento on conplanoorcamento.c60_anousu = c70_anousu
                   and conplanoorcamento.c60_codcon = o58_codele
             where c70_anousu = $exercicio
        ) as x
        order by 1
        ");
    }

    public function excluirContas($contas)
    {
        $this->exercicio = $contas[0]->c60_anousu;

        $contasSelecionadas = [];
        foreach ($contas as $conta) {
            $estruturalConta = new Estrutural($conta->c60_estrut);
            $estruturalAteNivel = $estruturalConta->getEstruturalAteNivel();
            ConplanoOrcamento::where('c60_anousu', '=', $conta->c60_anousu)
                ->where('c60_codcon', '!=', $conta->c60_codcon)
                ->where('c60_estrut', 'like', "$estruturalAteNivel%")
                ->get()
                ->each(function (ConplanoOrcamento $contaAnalitica) {
                    $this->contasAnaliticas[] = $contaAnalitica->c60_codcon;
                });
            $contasSelecionadas[] = $conta->c60_codcon;
        }

        //remove os desdobramentos dos elementos de despesa
        $this->removerVinculos($this->contasAnaliticas);
        $this->removerVinculos($contasSelecionadas);

        $this->removerContasPai($contas);
    }

    protected function removerVinculos(array $contas)
    {
        $this->removeVinculosDespesa($contas);
        $this->removeVinculosPlanejamento($contas);
        $this->removeVinculosOrcamento($contas);
        $this->removeVinculosContabilidade($contas);
    }

    protected function removeVinculosOrcamento(array $codigosContas)
    {
        parent::removeVinculosOrcamento($codigosContas);

        DB::table('orcamento.orcdotacao')
            ->where('o58_anousu', '>=', $this->exercicio)
            ->whereIn('o58_codele', $codigosContas)
            ->delete();

        DB::table('orcamento.orcelemento')
            ->where('o56_anousu', '>=', $this->exercicio)
            ->whereIn('o56_codele', $codigosContas)
            ->delete();
    }

    protected function removeVinculosPlanejamento(array $contas)
    {
        DB::table('planejamento.detalhamentoiniciativa')
            ->where('pl20_anoorcamento', '>=', $this->exercicio)
            ->whereIn('pl20_orcelemento', $contas)
            ->delete();

        DB::table('planejamento.fatorcorrecaodespesa')
            ->where('pl7_anoorcamento', '>=', $this->exercicio)
            ->whereIn('pl7_orcelemento', $contas)
            ->delete();
    }

    private function removeVinculosDespesa(array $contas)
    {
        $codigosPPA = DB::table('orcamento.ppadotacao')
            ->where('o08_ano', '>=', $this->exercicio)
            ->whereIn('o08_elemento', $contas)
            ->get()
            ->map(function ($ppadotacao) {
                return $ppadotacao->o08_sequencial;
            })->toArray();

        DB::table('orcamento.ppadotacaoorcdotacao')
            ->whereIn('o19_ppadotacao', $codigosPPA)
            ->delete();

        DB::table('orcamento.ppadotacao')
            ->whereIn('o08_sequencial', $codigosPPA)
            ->delete();
    }

    private function removerContasPai($contas)
    {
        foreach ($contas as $conta) {
            $estruturalConta = new Estrutural($conta->c60_estrut);
            $continua = true;
            while ($continua) {
                $estruturalConta = $this->buscaContaPai($estruturalConta->getEstrutural());
                $filtros = ['estrutural' => $estruturalConta->getEstrutural(), "exercicio" => $this->exercicio];
                $contasOrcamento = $this->getContas($filtros);
                if ($contasOrcamento->count() === 1) {
                    $contaOrcamento = $contasOrcamento->shift();
                    $this->removerVinculos([$contaOrcamento->c60_codcon]);
                } else {
                    $continua = false;
                }
            }
        }
    }


    private function buscaContaPai($estrutural)
    {
        $estruturalConta = new Estrutural($estrutural);
        return $estruturalConta->getEstruturalPai();
    }

    /**
     * @param array $estruturaisComMovimento
     * @param ConplanoOrcamento $conta
     * @return bool
     */
    public function temMovimentacao(array $estruturaisComMovimento, ConplanoOrcamento $conta)
    {
        $dadoBalancete = collect($estruturaisComMovimento)->filter(function ($estrutural) use ($conta) {
            $estruturalBalancete = new Estrutural($estrutural->estrutural);
            $estruturalConta = new Estrutural($conta->c60_estrut);
            return $estruturalConta->getEstruturalAteNivel() === $estruturalBalancete->getEstruturalAteNivel();
        })->shift();

        // se possui execução, não pode excluir
        if (!is_null($dadoBalancete)) {
            return true;
        }
        return false;
    }
}
