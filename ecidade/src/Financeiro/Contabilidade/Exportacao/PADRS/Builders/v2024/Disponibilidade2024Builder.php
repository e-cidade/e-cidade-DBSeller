<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\Disponibilidade2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023\Disponibilidade;

class Disponibilidade2024Builder extends Disponibilidade2023Builder
{

    protected function create()
    {
        $this->layout = new Disponibilidade();
    }

    protected function processar()
    {
        $agencia = $this->buildAgencia();
        $conta = $this->buildConta();
        $this->layout->setEstrutural($this->formatar($this->dados['estrutural'], 20, '0', STR_PAD_LEFT))
            ->setOrgaoUnidade($this->formataNumerico($this->dados['orgao_unidade'], 4))
            ->setSubrecurso($this->formataNumerico(0, 4))
            ->setBanco($this->formataNumerico($this->dados['banco'], 5))
            ->setAgencia($this->formataNumerico($agencia, 5))
            ->setConta($this->formatar($conta, 20, '0', STR_PAD_LEFT))
            ->setTipoConta($this->buildTipoConta())
            ->setClassificacaoConta($this->buildClassificacaoConta())
            ->setComplementoAntigo($this->formataNumerico(0, 4))
            ->setSiconfi($this->formataNumerico($this->dados['siconfi'], 4))
            ->setComplemento($this->formataNumerico($this->dados['complemento'], 4));
    }
}
