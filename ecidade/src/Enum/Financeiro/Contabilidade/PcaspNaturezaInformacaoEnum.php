<?php

namespace ECidade\Enum\Financeiro\Contabilidade;

use ECidade\Enum\Enum;
use Exception;

class PcaspNaturezaInformacaoEnum extends Enum
{

    const PATRIMONIAL = 'P';
    const ORCAMENTARIO = 'O';
    const CONTROLE = 'C';

    public static function getPorEstrutural($estrutural)
    {
        $classe = substr($estrutural, 0, 1);
        switch ($classe) {
            case 1:
            case 2:
            case 3:
            case 4:
                return new self("P");
            case 5:
            case 6:
                return new self("O");
            case 7:
            case 8:
                return new self("C");
        }
    }
}
