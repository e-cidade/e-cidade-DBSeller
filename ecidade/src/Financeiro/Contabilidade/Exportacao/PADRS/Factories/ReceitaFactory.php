<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\Receita2023Service;

class ReceitaFactory
{
    public static function getService($exercicio, array $instituicoes, $dataInicio, $dataFim)
    {
        return new Receita2023Service($exercicio, $instituicoes, $dataInicio, $dataFim);
    }
}
