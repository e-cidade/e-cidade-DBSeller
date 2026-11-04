<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\BalanceteReceita2023Service;

class BalanceteReceitaFactory
{
    /**
     * @param $anousu
     * @param array $instituicoes
     * @param $data_ini
     * @param $data_fim
     * @return BalanceteReceita2023Service
     */
    public static function getService($anousu, $instituicoes, $data_ini, $data_fim)
    {
        return new BalanceteReceita2023Service($anousu, $instituicoes, $data_ini, $data_fim);
    }
}
