<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use App\Domain\Financeiro\Contabilidade\Relatorios\Mapeamento\MapeamentoPlanoContasCsv;
use App\Domain\Financeiro\Contabilidade\Services\ManutencaoPlanoOrcamentarioDespesaService;

class MapeamentoPlanoDespesaService
{
    protected $filtros = [];
    protected $contasSemVinculo = [];
    protected $contasComVinculo = [];
    protected $contasSemVinculoComMovimentacao = [];
    /**
     * @var ManutencaoPlanoOrcamentarioDespesaService
     */
    private $service;

    public function __construct()
    {
        $this->service = new ManutencaoPlanoOrcamentarioDespesaService();
    }

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
                $contasVinculadas = $conta->planoUniaoDespesa;
            } else {
                $contasVinculadas = $conta->planoEstadualDespesa;
            }

            if ($contasVinculadas->count() === 0) {
                if ($this->service->temMovimentacao($this->getEstruturaisComMovimento(), $conta)) {
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
        return ConplanoOrcamento::contasDespesaAPartirElemento($this->filtros['exercicio']);
    }

    private function translate(ConplanoOrcamento $conta, PlanoDespesa $planoReceita)
    {
        return (object)[
            'estrutural_ecidade' => $conta->c60_estrut,
            'nome_ecidade' => $conta->c60_descr,
            'estrutural_governo' => $planoReceita->conta,
            'nome_governo' => $planoReceita->nome,
        ];
    }

    protected function getEstruturaisComMovimento()
    {
        if (empty($this->balancete)) {
            $this->balancete = $this->service->estruturaisComMovimento($this->filtros['exercicio']);
        }

        return $this->balancete;
    }

    public function getContasSemVinculoComMovimentacao()
    {
        if (empty($this->contasSemVinculoComMovimentacao) &&
            empty($this->contasSemVinculo) &&
            empty($this->contasComVinculo)
        ) {
            $this->service = new ManutencaoPlanoOrcamentarioDespesaService();
            $this->processar();
        }
        return $this->contasSemVinculoComMovimentacao;
    }
}
