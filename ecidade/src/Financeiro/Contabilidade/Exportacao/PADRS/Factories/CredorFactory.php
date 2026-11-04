<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\Credor2023Service;

class CredorFactory
{
    public static function getService($exercicio, array $instituicoes, $dataInicio, $dataFim)
    {
        return new Credor2023Service($exercicio, $instituicoes, $dataInicio, $dataFim);
    }
}
