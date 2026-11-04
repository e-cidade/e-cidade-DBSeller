<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\BalanceteVerificacao;

class BalanceteVerificacao2023Builder extends PadBuilder
{
    /**
     * @var BalanceteVerificacao
     */
    protected $layout;

    protected function create()
    {
        $this->layout = new BalanceteVerificacao();
    }

    protected function processar()
    {
        $this->layout->setEstrutural($this->formatar($this->dados['estrutural'], 20, '0', STR_PAD_LEFT));
        $this->layout->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4));
        $this->layout->setSaldoAnteriorDebito($this->formataValor($this->dados['saldo_anterior_debito'], 13));
        $this->layout->setSaldoAnteriorCredito($this->formataValor($this->dados['saldo_anterior_credito'], 13));
        $this->layout->setSaldoDebito($this->formataValor($this->dados['saldo_debito'], 13));
        $this->layout->setSaldoCredito($this->formataValor($this->dados['saldo_credito'], 13));
        $this->layout->setSaldoFinalDebito($this->formataValor($this->dados['saldo_final_debito'], 13));
        $this->layout->setSaldoFinalCredito($this->formataValor($this->dados['saldo_final_credito'], 13));
        $this->layout->setNome($this->formataCaractere($this->dados['nome'], 148));
        $this->layout->setTipo($this->dados['sintetica'] ? 'S' : 'A');
        $this->layout->setNivel($this->formataNumerico($this->dados['nivel'], 2));
        $this->layout->setNaturezaInformacao($this->dados['natureza_informacao']);
        $this->layout->setIndicadorSuperavit($this->dados['indicador_superavit']);
        $this->layout->setSubrecurso($this->formataNumerico($this->dados['subrecurso'], 4));

        $complementoAntigo = $this->dados['complemento'];
        $complementoValido = [3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0000];
        if (!in_array($this->dados['complemento'], $complementoValido)) {
            $complementoAntigo = '0000';
        }

        if ($this->layout->getSubrecurso() === '0000') {
            $complementoAntigo = '0000';
        }

        $this->layout->setComplementoAntigo($this->formataNumerico($complementoAntigo, 4));
        $this->layout->setSiconfi($this->formataNumerico($this->dados['siconfi'], 4));
        $this->layout->setComplemento($this->formataNumerico($this->dados['complemento'], 4));
    }
}
