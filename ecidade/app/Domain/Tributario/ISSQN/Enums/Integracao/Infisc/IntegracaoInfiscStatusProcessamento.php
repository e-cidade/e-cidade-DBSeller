<?php

namespace App\Domain\Tributario\ISSQN\Enums\Integracao\Infisc;

use ECidade\Enum\Enum;

class IntegracaoInfiscStatusProcessamento extends Enum
{
    const PENDENTE = "PENDENTE";
    const PROCESSADO = "PROCESSADO";
    const ERRO = "ERRO";
}
