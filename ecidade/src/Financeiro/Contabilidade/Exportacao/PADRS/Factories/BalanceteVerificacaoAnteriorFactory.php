<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024\BalanceteVerificacaoAnterior2024Service;

class BalanceteVerificacaoAnteriorFactory
{
    public static function getService($anousu, $instituicoes, $data_ini, $data_fim)
    {
        return new BalanceteVerificacaoAnterior2024Service($anousu, $instituicoes, $data_ini, $data_fim);
    }
}
