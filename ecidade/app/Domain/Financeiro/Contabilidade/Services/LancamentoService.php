<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\DocumentoLancamento;
use App\Domain\Financeiro\Contabilidade\Models\Lancamento;
use App\Domain\Financeiro\Contabilidade\Models\LancamentoComplemento;
use App\Domain\Financeiro\Contabilidade\Models\LancamentoInstituicao;
use App\Domain\Financeiro\Contabilidade\Models\LancamentoContas;
use App\Domain\Financeiro\Contabilidade\Models\VinculoEventosContabeis;
use App\Domain\Financeiro\Contabilidade\VO\LancamentoContabilContasVO;
use App\Domain\Financeiro\Contabilidade\VO\LancamentoContabilVO;

/**
 * Class LancamentoService
 * Responsável por criar os lançamentos contábeis no laravel.
 *
 * Hoje
 */
class LancamentoService
{
    /**
     * @param LancamentoContabilVO $lancamentoVO
     * @return Lancamento
     */
    public function criar(LancamentoContabilVO $lancamentoVO)
    {
        $lancamento = new Lancamento();
        $lancamento->c70_anousu = $lancamentoVO->exercicio;
        $lancamento->c70_data = $lancamentoVO->data;
        $lancamento->c70_valor = $lancamentoVO->valor;
        $lancamento->save();

        if (!empty($lancamentoVO->instituicao)) {
            $this->vincularInstituicao($lancamento, $lancamentoVO->instituicao);
        }
        if (!empty($lancamentoVO->documento)) {
            $this->vincularDocumento($lancamento, $lancamentoVO->documento);
        }
        if (!empty($lancamentoVO->observacao)) {
            $this->vincularComplemento($lancamento, $lancamentoVO->observacao);
        }
        $contas = $lancamentoVO->getContas();
        if (!empty($contas)) {
            $this->salvarContas($lancamento, $contas);
        }

        return $lancamento;
    }

    /**
     * @param Lancamento $lancamento
     * @param integer $instituicao
     * @return void
     */
    private function vincularInstituicao(Lancamento $lancamento, $instituicao)
    {
        $model = new LancamentoInstituicao();
        $model->c02_instit = $instituicao;
        $model->c02_codlan = $lancamento->c70_codlan;
        $model->save();
    }

    /**
     * @param Lancamento $lancamento
     * @param integer $documento
     * @return void
     */
    private function vincularDocumento(Lancamento $lancamento, $documento)
    {
        $model = new DocumentoLancamento();
        $model->c71_codlan = $lancamento->c70_codlan;
        $model->c71_data = $lancamento->c70_data;
        $model->c71_coddoc = $documento;
        $model->save();
    }

    /**
     * @param Lancamento $lancamento
     * @param string $descricao
     * @return void
     */
    private function vincularComplemento(Lancamento $lancamento, $descricao)
    {
        $model = new LancamentoComplemento();
        $model->c72_codlan = $lancamento->c70_codlan;
        $model->c72_complem = $descricao;
        $model->save();
    }

    /**
     * @param Lancamento $lancamento
     * @param array $contas
     * @return void
     */
    private function salvarContas(Lancamento $lancamento, array $contas)
    {
        /**
         * @var $conta LancamentoContabilContasVO
         */
        foreach ($contas as $conta) {
            $model = new LancamentoContas();

            $model->c69_anousu = $lancamento->c70_anousu;
            $model->c69_codlan = $lancamento->c70_codlan;
            $model->c69_data = $lancamento->c70_data->format('Y-m-d');
            $model->c69_codhist = $conta->historico;
            $model->c69_credito = $conta->contaCredito;
            $model->c69_debito = $conta->contaDebito;
            $model->c69_valor = $conta->valor;
            $model->c69_ordem = $conta->ordem;
            $model->save();
            /**
             * @todo mas para frente, verificar recurso por conta
             */
        }
    }
}
