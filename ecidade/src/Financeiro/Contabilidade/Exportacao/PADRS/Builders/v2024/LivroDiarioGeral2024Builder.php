<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\LivroDiarioGeral2023Builder;

class LivroDiarioGeral2024Builder extends LivroDiarioGeral2023Builder
{

    protected function processar()
    {
        parent::processar();
        $this->layout->setFonteRecurso($this->formataNumerico('0', 4));
        $this->layout->setComplemento($this->formataNumerico('0', 4));
    }
}
