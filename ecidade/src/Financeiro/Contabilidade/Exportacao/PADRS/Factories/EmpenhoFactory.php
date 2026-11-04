<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\EmpenhoService2023;

class EmpenhoFactory
{

    /**
     * @param $exercicio
     * @param $instituicoes
     * @param $dataInicial
     * @param $dataFinal
     * @return
     */
    public static function getService($exercicio, $instituicoes, $dataInicial, $dataFinal)
    {
        return new EmpenhoService2023($exercicio, $instituicoes, $dataInicial, $dataFinal);
    }
}
