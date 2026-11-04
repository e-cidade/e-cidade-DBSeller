<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use App\Domain\Financeiro\Contabilidade\Relatorios\Mapeamento\MapeamentoPlanoContasCsv;

class MapeamentoPcaspService
{
    protected $filtros = [];
    protected $contasSemVinculo = [];
    protected $contasComVinculo = [];
    protected $contasSemVinculoComMovimentacao = [];

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
        $relatorio->setNomeArquivo('mapeamento-pcasp');
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
                $contasVinculadas = $conta->pcaspUniao;
            } else {
                $contasVinculadas = $conta->pcaspEstadual;
            }
            if ($contasVinculadas->count() === 0) {
                $temMovimentacao = $conta->getReduzidos()->filter(function (ConplanoReduzido $reduzido) {
                        return $reduzido->possueMovimentacao($reduzido->c61_reduz, $reduzido->c61_anousu);
                })->count() > 0;

                if ($temMovimentacao) {
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
        return Conplano::apenasAnaliticas()
            ->where('c60_anousu', $this->filtros['exercicio'])
            ->orderBy('c60_estrut')
            ->get();
    }

    private function translate(Conplano $conta, Pcasp $pcasp)
    {
        return (object)[
            'estrutural_ecidade' => $conta->c60_estrut,
            'nome_ecidade' => $conta->c60_descr,
            'estrutural_governo' => $pcasp->conta,
            'nome_governo' => $pcasp->nome,
        ];
    }

    /**
     * @return array|mixed
     */
    public function getContasSemVinculoComMovimentacao()
    {
        if (empty($this->contasSemVinculoComMovimentacao)
            && empty($this->contasSemVinculo)
            && empty($this->contasComVinculo)) {
            $this->processar();
        }

        return $this->contasSemVinculoComMovimentacao;
    }
}
