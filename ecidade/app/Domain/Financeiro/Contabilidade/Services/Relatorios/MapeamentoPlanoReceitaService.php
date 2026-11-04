<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Contabilidade\Relatorios\Mapeamento\MapeamentoPlanoContasCsv;

class MapeamentoPlanoReceitaService
{

    protected $filtros = [];
    protected $contasSemVinculo = [];
    protected $contasComVinculo = [];
    protected $contasSemVinculoComMovimentacao = [];

    protected $balancete = [];

    /**
     * @param array $filtros
     * @return void
     */
    public function filtros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    /**
     * @return array
     */
    public function emitir()
    {
        $this->processar();
        $relatorio = new MapeamentoPlanoContasCsv();
        $relatorio->setExercicio($this->filtros['exercicio']);
        $relatorio->setTipoPlano($this->filtros['tipoPlano']);
        $relatorio->setContas($this->contasSemVinculo, $this->contasComVinculo);
        $relatorio->setContasSemVinculoComMovimentacao($this->contasSemVinculoComMovimentacao);
        return $relatorio->emitir();
    }

    private function processar()
    {
        $contas = $this->getContas();

        foreach ($contas as $conta) {
            if ($this->filtros['tipoPlano'] === PlanoContas::PLANO_UNIAO) {
                $contasVinculadas = $conta->planoUniaoReceita;
            } else {
                $contasVinculadas = $conta->planoEstadualReceita;
            }

            if ($contasVinculadas->count() === 0) {
                if ($this->temMovimentacao($conta)) {
                    $this->contasSemVinculoComMovimentacao[] = $conta;
                } else {
                    $this->contasSemVinculo[] = $conta;
                }
            } else {
                $this->contasComVinculo[] = $this->translate($conta, $contasVinculadas->shift());
            }
        }
    }

    private function getContas()
    {
        return ConplanoOrcamento::query()
            ->orderBy('c60_estrut')
            ->select('*')
            ->apenasAnaliticas()
            ->apenasReceita()
            ->when(!empty($this->filtros['exercicio']), function ($query) {
                $query->where('c60_anousu', '=', $this->filtros['exercicio']);
            })
            ->get();
    }

    private function translate(ConplanoOrcamento $conta, PlanoReceita $planoReceita)
    {
        return (object)[
            'estrutural_ecidade' => $conta->c60_estrut,
            'nome_ecidade' => $conta->c60_descr,
            'estrutural_governo' => $planoReceita->conta,
            'nome_governo' => $planoReceita->nome,
        ];
    }

    protected function getBalancete()
    {
        if (empty($this->balancete)) {
            $this->balancete = $this->processaBalanceteReceita();
        }
        return $this->balancete;
    }

    /**
     * Processa o balancete do e-cidade para comparar as contas que possuem mapeamento.
     * @return array
     */
    private function processaBalanceteReceita()
    {
        $exercicio = $this->filtros['exercicio'];
        $filtros = [
            "dataInicio" => sprintf('01/01/%s', $exercicio),
            "dataFinal" => sprintf('31/12/%s', $exercicio),
            "nivelAgrupar" => 0,
            "apenasComMovimentacao" => 1,
            "ementario" => 'ecidade'
        ];

        $service = new BalanceteReceitaComplementoService();
        $service->setFiltrosRequest($filtros);

        $service->setInstituicoes(DBConfig::select(['codigo', 'nomeinst'])->get());
        return $service->getArvore();
    }

    private function temMovimentacao($conta)
    {
        $balancete = $this->getBalancete();
        $dadoBalancete = collect($balancete)->filter(function ($contaBalancete) use ($conta) {
            return $contaBalancete->natureza === $conta->c60_estrut;
        })->shift();

        if (!is_null($dadoBalancete)) {
            $temSaldo = ($dadoBalancete->previsao_atualizada != 0 ||
                $dadoBalancete->arrecadado_periodo != 0 ||
                $dadoBalancete->arrecadado_acumulado != 0
            );
            return $temSaldo;
        }

        return false;
    }

    public function getContasSemVinculoComMovimentacao()
    {
        if (empty($this->contasSemVinculoComMovimentacao) &&
            empty($this->contasSemVinculo) &&
            empty($this->contasComVinculo)
        ) {
            $this->processar();
        }
        return $this->contasSemVinculoComMovimentacao;
    }
}
