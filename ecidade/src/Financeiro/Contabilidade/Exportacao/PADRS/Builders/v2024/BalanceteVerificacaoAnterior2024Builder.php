<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\PadBuilder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2024\BalanceteVerificacaoAnterior;

class BalanceteVerificacaoAnterior2024Builder extends PadBuilder
{
    protected function create()
    {
        $this->layout = new  BalanceteVerificacaoAnterior();
    }

    protected function processar()
    {
        $this->layout->setEstrutural(
            $this->formatar($this->dados['estrutural'], 20, '0', STR_PAD_LEFT)
        );
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
    }
}
