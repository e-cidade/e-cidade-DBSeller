<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\Receita;

class Receita2023Builder extends PadBuilder
{
    /**
     * @var Receita
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new Receita();
    }

    protected function processar()
    {
        $meta1Bimestre = $this->dados['bimestre_1'];
        $meta2Bimestre = $this->dados['bimestre_2'];
        $meta3Bimestre = $this->dados['bimestre_3'];
        $meta4Bimestre = $this->dados['bimestre_4'];
        $meta5Bimestre = $this->dados['bimestre_5'];
        $meta6Bimestre = $this->dados['bimestre_6'];

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
        $this->layout->setMeta1Bimestre($this->formataValor($meta1Bimestre, 12));
        $this->layout->setMeta2Bimestre($this->formataValor($meta2Bimestre, 12));
        $this->layout->setMeta3Bimestre($this->formataValor($meta3Bimestre, 12));
        $this->layout->setMeta4Bimestre($this->formataValor($meta4Bimestre, 12));
        $this->layout->setMeta5Bimestre($this->formataValor($meta5Bimestre, 12));
        $this->layout->setMeta6Bimestre($this->formataValor($meta6Bimestre, 12));
        $this->layout->setCp($this->formataNumerico($this->dados['cp'], 3));
        $this->layout->setFonteRecurso($this->formataNumerico($this->dados['siconfi'], 4));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento'], 4));

        if ($this->modeloMGS) {
            $this->layout->setCampoObsoleto1($this->formataNumerico($this->dados['subrecurso'], 4));
            $this->layout->setCampoObsoleto2($this->formataNumerico($this->dados['complemento'], 4));
        }
    }
}
