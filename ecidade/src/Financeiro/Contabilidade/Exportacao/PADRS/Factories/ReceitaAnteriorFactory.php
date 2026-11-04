<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\ReceitaAnterior2024Service;

class ReceitaAnteriorFactory
{
    public static function getService($exercicio, array $instituicoes, $dataInicio, $dataFim)
    {
        return new ReceitaAnterior2024Service($exercicio, $instituicoes, $dataInicio, $dataFim);
    }
}
