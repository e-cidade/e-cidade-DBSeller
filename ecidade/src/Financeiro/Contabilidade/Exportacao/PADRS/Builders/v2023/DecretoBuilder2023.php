<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2021\DecretoBuilder2021;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2022\Decreto;

class DecretoBuilder2023 extends DecretoBuilder2021
{

    protected function create()
    {
        $this->layout = new Decreto();
    }

    protected function processar()
    {
        $tipoCredito = $this->identificaTipoCredito($this->dados['tipo_suplementacao']);
        $origem = $this->identificaOrigemRecursos($this->dados['tipo_suplementacao'], $this->dados['entre_entidades']);
        $dataReabertura = $this->dados['data_reabertura_credito_adicional'] ?: '00000000';
        $alteracaoOrcamentaria = $this->identificaAlteracaoOrcamentaria($this->dados['tipo_suplementacao']);

        $this->layout->setNumeroLei($this->formataCaractere($this->dados['numero_lei'], 20));
        $this->layout->setDataLei($this->formataData($this->dados['data_lei']));
        $this->layout->setNumeroDecreto($this->formataCaractere($this->dados['numero_decreto'], 20));
        $this->layout->setDataDecreto($this->formataData($this->dados['data_decreto']));
        $this->layout->setValorCreditoAdicional($this->formataValor($this->dados['valor_credito'], 13));
        $this->layout->setValorReducaoDotacoes($this->formataValor($this->dados['valor_reducao'], 13));
        $this->layout->setTipoCreditoAdicional($tipoCredito);
        $this->layout->setOrigemRecurso($origem);

        $this->layout->setAlteracoesOrcamentarias($alteracaoOrcamentaria);
        $this->layout->setDataReaberturaCreditoAdicional($this->formataData($dataReabertura));
        $this->layout->setValorAlteracoeOrcamentarias(
            $this->formataValor($this->dados['valor_alteracao_orcamentaria'], 13)
        );
        $this->layout->setValorSaldoReaberto($this->formataValor($this->dados['valor_saldo_reaberto'], 13));


        $this->layout->setRecursoSuplementacao('0000');
        $this->layout->setRecursoReducoes('0000');

        if (array_key_exists('recurso_suplementacao', $this->dados)) {
            $recursoSiconfi = substr($this->dados['recurso_suplementacao'], 1);
            $this->layout->setRecursoSuplementacaoSiconfi($this->formataNumerico($recursoSiconfi, 4));
        }

        if (array_key_exists('recurso_reducao', $this->dados)) {
            $recursoSiconfi = substr($this->dados['recurso_reducao'], 1);
            $this->layout->setRecursoReducoesSiconfi($this->formataNumerico($recursoSiconfi, 4));
        }
    }
}
