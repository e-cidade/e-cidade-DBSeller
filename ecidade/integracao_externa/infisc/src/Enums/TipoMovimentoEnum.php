<?php

namespace IntegracaoExterna\Infisc\Enums;

class TipoMovimentoEnum extends \ECidade\Enum\Enum
{
    const ORIGINAL = "1";
    const COMPLEMENTAR = "2";
    const SEM_MOVIMENTO = "3";
}
