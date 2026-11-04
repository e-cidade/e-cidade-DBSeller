<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\BalanceteReceitaAnteriorService;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2022\BalanceteReceitaAnteriorService2022;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\BalanceteReceitaAnterior2024Service;

class BalanceteReceitaAnteriorFactory
{
    /**
     * @param $exercicio
     * @param array $instituicoes
     * @return BalanceteReceitaAnteriorService|BalanceteReceitaAnteriorService2022|BalanceteReceitaAnterior2024Service
     */
    public static function getService($exercicio, array $instituicoes)
    {

        if ($exercicio < 2021) {
            return new BalanceteReceitaAnteriorService($instituicoes, $exercicio);
        }

        if ($exercicio > 2021 && $exercicio <= 2022) {
            return new BalanceteReceitaAnteriorService2022($instituicoes, $exercicio);
        }
        /**
         * Em 2024 houve uma alteramão no layout do pad.
         * Como essa factory trata de exercício anterior, o exercício recebido é: ano atual -1
         */
        $dataInicial = "{$exercicio}-01-01";
        $dataFinal = "{$exercicio}-12-31";
        return new BalanceteReceitaAnterior2024Service($exercicio, $instituicoes, $dataInicial, $dataFinal);
    }
}
