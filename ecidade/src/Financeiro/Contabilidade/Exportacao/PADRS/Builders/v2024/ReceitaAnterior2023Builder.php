<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2024\ReceitaAnterior;

class ReceitaAnterior2023Builder extends PadBuilder
{

    protected function create()
    {
        $this->layout = new ReceitaAnterior();
    }

    protected function processar()
    {
        $this->layout->setFonte($this->formataNumerico($this->dados['natureza'], 20));
        $this->layout->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4));
        $this->layout->setRealizadaJaneiro($this->formataValor($this->dados['arrecadado_jan'], 13));
        $this->layout->setRealizadaFevereiro($this->formataValor($this->dados['arrecadado_fev'], 13));
        $this->layout->setRealizadaMarco($this->formataValor($this->dados['arrecadado_mar'], 13));
        $this->layout->setRealizadaAbril($this->formataValor($this->dados['arrecadado_abr'], 13));
        $this->layout->setRealizadaMaio($this->formataValor($this->dados['arrecadado_mai'], 13));
        $this->layout->setRealizadaJunho($this->formataValor($this->dados['arrecadado_jun'], 13));
        $this->layout->setRealizadaJulho($this->formataValor($this->dados['arrecadado_jul'], 13));
        $this->layout->setRealizadaAgosto($this->formataValor($this->dados['arrecadado_ago'], 13));
        $this->layout->setRealizadaSetembro($this->formataValor($this->dados['arrecadado_set'], 13));
        $this->layout->setRealizadaOutubro($this->formataValor($this->dados['arrecadado_out'], 13));
        $this->layout->setRealizadaNovembro($this->formataValor($this->dados['arrecadado_nov'], 13));
        $this->layout->setRealizadaDezembro($this->formataValor($this->dados['arrecadado_dez'], 13));
        $this->layout->setCp($this->formataNumerico($this->dados['cp'], 3));
        $this->layout->setFonteRecurso($this->formataNumerico($this->dados['siconfi'], 4));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento'], 4));

        if ($this->modeloMGS) {
            $this->layout->setCampoObsoleto1($this->formataNumerico($this->dados['subrecurso'], 4));
            $this->layout->setCampoObsoleto2($this->formataNumerico($this->dados['complemento'], 4));
        }
    }
}
