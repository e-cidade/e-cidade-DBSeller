<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\BalanceteVerificacao2023Builder;

class BalanceteVerificacao2024Builder extends BalanceteVerificacao2023Builder
{

    /**
     * @return void
     */
    protected function processar()
    {
        parent::processar();
        $this->layout->setSubrecurso($this->formataNumerico(0, 4));
        $this->layout->setComplementoAntigo($this->formataNumerico(0, 4));
    }

    /**
     * @return BalanceteVerificacao2024Builder
     */
    protected function getBuilder()
    {
        return new BalanceteVerificacao2024Builder();
    }
}
