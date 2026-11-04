<?php

namespace ECidade\Tributario\Arrecadacao\Enums;

use ECidade\Enum\Enum;

class TipoOrdenacaoCgfEnum extends Enum
{
    const NUMPRE = 1;
    const NUMPAR = 2;
    const RECEITA = 3;
    const DATA_VENCIMENTO = 4;
    const DATA_OPERACAO = 5;
    const ORIGEM = 6;
    const INICIAL = 7;
    const DATA_INICIAL = 8;
    const PROCESSO_FORO = 9;
    const CDA = 10;

    /**
     * @param int $tipo
     * @return int
     */
    public static function getEnumByTipo($tipo)
    {
        switch ((int) $tipo) {
            case 1:
                return TipoOrdenacaoCgfEnum::NUMPRE;
            case 2:
                return TipoOrdenacaoCgfEnum::NUMPAR;
            case 3:
                return TipoOrdenacaoCgfEnum::RECEITA;
            case 4:
                return TipoOrdenacaoCgfEnum::DATA_VENCIMENTO;
            case 5:
                return TipoOrdenacaoCgfEnum::DATA_OPERACAO;
            case 6:
                return TipoOrdenacaoCgfEnum::ORIGEM;
            case 7:
                return TipoOrdenacaoCgfEnum::INICIAL;
            case 8:
                return TipoOrdenacaoCgfEnum::DATA_INICIAL;
            case 9:
                return TipoOrdenacaoCgfEnum::PROCESSO_FORO;
            case 10:
                return TipoOrdenacaoCgfEnum::CDA;
        }
    }
}
