<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Enums;

use ECidade\Enum\Enum;

class FiltrosEnum extends Enum
{
    const IGUAL = '=';
    const DIFERENTE = '!=';
    const MAIOR = '>';
    const MENOR = '<';
    const MAIOR_IGUAL = '>=';
    const MENOR_IGUAL = '<=';
    const CONTEM = 'in';
    const NULO = 'is null';
    const PREENCHIDO = 'is not null';
}
