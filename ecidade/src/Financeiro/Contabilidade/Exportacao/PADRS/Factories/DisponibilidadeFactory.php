<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\Disponibilidade2023Service;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\Disponibilidade2024Service;

class DisponibilidadeFactory
{
    public static function getService($exercicio, array $instituicoes, $dataInicio, $dataFim)
    {
        if ($exercicio <= 2023) {
            return new Disponibilidade2023Service($exercicio, $instituicoes, $dataInicio, $dataFim);
        }

        return new Disponibilidade2024Service($exercicio, $instituicoes, $dataInicio, $dataFim);
    }
}
