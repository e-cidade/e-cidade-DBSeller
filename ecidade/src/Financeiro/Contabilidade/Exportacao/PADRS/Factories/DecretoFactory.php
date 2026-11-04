<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\DecretoService;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2022\DecretoService2022;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\DecretoService2023;

class DecretoFactory
{
    /**
     * @param $exercicio
     * @param $instituicoes
     * @param $dataInicial
     * @param $dataFinal
     * @return DecretoService|DecretoService2022
     */
    public static function getService($exercicio, $instituicoes, $dataInicial, $dataFinal)
    {
        switch ($exercicio) {
            case 2022:
                return new DecretoService2022($instituicoes, $exercicio, $dataInicial, $dataFinal);
            case 2023:
            default:
                return new DecretoService2023($instituicoes, $exercicio, $dataInicial, $dataFinal);
        }
    }
}
