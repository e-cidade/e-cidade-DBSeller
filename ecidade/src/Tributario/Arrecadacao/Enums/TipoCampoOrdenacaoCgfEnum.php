<?php

namespace ECidade\Tributario\Arrecadacao\Enums;

use ECidade\Enum\Enum;

class TipoCampoOrdenacaoCgfEnum extends Enum
{
    const NUMERO = 1;
    const DATA = 2;

    /**
     * @param int $tipo
     * @return int
     */
    public static function getEnumByTipo($tipo)
    {
        switch ((int) $tipo) {
            case 1:
                return TipoCampoOrdenacaoCgfEnum::NUMERO;
            case 2:
                return TipoCampoOrdenacaoCgfEnum::DATA;
        }
    }
}
