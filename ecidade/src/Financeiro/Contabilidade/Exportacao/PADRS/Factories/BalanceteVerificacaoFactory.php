<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\BalanceteVerificacao2023Service;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\BalanceteVerificacao2024Service;

class BalanceteVerificacaoFactory
{
    /**
     * @param $anousu
     * @param $instituicoes
     * @param $data_ini
     * @param $data_fim
     * @return BalanceteVerificacao2023Service
     */
    public static function getService($anousu, $instituicoes, $data_ini, $data_fim)
    {
        if ($anousu <= 2023) {
            return new BalanceteVerificacao2023Service($anousu, $instituicoes, $data_ini, $data_fim);
        }
        return new BalanceteVerificacao2024Service($anousu, $instituicoes, $data_ini, $data_fim);
    }
}
