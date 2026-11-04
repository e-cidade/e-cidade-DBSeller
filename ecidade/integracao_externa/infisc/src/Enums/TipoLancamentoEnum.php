<?php

namespace IntegracaoExterna\Infisc\Enums;

class TipoLancamentoEnum extends \ECidade\Enum\Enum
{
    const PAGAMENTO = "1";
    const CANCELAMENTO = "2";
    const REABERTURA_COMPETENCIA = "3";
    const CANCELAMENTO_SEM_MOVIMENTO = "4";
}
