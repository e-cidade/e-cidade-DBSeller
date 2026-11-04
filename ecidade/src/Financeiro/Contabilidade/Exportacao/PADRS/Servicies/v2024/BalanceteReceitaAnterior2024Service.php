<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024\BalanceteReceitaAnterior2024Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\BalanceteReceita2023Service;

class BalanceteReceitaAnterior2024Service extends BalanceteReceita2023Service
{
    protected $fileName = 'BREC_ANT.TXT';
    protected function getBuilder()
    {
        return new BalanceteReceitaAnterior2024Builder();
    }
}
