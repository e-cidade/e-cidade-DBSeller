<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\RDExtra2023Service;

class RDExtraFactory
{
    public static function getService($anousu, $instituicoes, $data_ini, $data_fim)
    {
        return new RDExtra2023Service($anousu, $instituicoes, $data_ini, $data_fim);
    }
}
