<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024\ReceitaAnterior2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\Receita2023Service;

class ReceitaAnterior2024Service extends Receita2023Service
{
    protected $fileName = 'REC_ANT.TXT';

    protected function getBuilder()
    {
        return new ReceitaAnterior2023Builder();
    }
}
