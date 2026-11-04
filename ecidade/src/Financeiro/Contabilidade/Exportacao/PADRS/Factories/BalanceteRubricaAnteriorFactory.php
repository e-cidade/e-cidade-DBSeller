<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\BalanceteRubricaAnteriorService;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2022\BalanceteRubricaAnteriorService2022;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\BalanceteRubricaAnteriorService2024;

class BalanceteRubricaAnteriorFactory
{

    public static function getService($exercicio, array $instituicoes)
    {
        if ($exercicio < 2021) {
            return new BalanceteRubricaAnteriorService($instituicoes, $exercicio);
        }

        if ($exercicio < 2023) {
            return new BalanceteRubricaAnteriorService2022($instituicoes, $exercicio);
        }

        /**
         * Em 2024 houve uma alteramão no layout do pad.
         * Como essa factory trata de exercício anterior, o exercício recebido é: ano atual -1
         */
        return new BalanceteRubricaAnteriorService2024($instituicoes, $exercicio);
    }
}
