<?php

namespace ECidade\Enum\Financeiro\Contabilidade;

use ECidade\Enum\Enum;

/**
 * Classe implementada para
 */
class PcaspIndicadorSuperavitEnum extends Enum
{
    const FINANCEIRO = 'F';
    const PATRIMONIAL = 'P';

    /**
     *
     * @param $valor
     * @return PcaspIndicadorSuperavitEnum
     */
    public static function getInstance($valor)
    {
        switch ($valor) {
            case 'F':
                return new self("F");
            case 'N':
            case 'P':
            case 'F/P':
            default:
                return new self("P");
        }
    }
}
