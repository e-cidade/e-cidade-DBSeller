<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\LivroDiarioGeralService;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\LivroDiarioGeral2024Service;

class LivroDiarioGeralFactory
{
    public static function getService($exercicio, $dataInicio, $dataFim, array $instituicoes)
    {
        if ($exercicio <= 2023) {
            return new LivroDiarioGeralService($exercicio, $dataInicio, $dataFim, $instituicoes);
        }

        return new LivroDiarioGeral2024Service($exercicio, $dataInicio, $dataFim, $instituicoes);
    }
}
