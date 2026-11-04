<?php

namespace App\Domain\Tributario\ISSQN\Enums\Integracao\Infisc;

class IntegracaoInfiscTipoDebito extends \ECidade\Enum\Enum
{
    const ISS_VARIAVEL = 3;
    const ISS_RETIDO = 33;

    /**
     * @throws \ReflectionException
     */
    public static function deveIntegrar($tipoDebito)
    {
        if (self::search($tipoDebito)) {
            return true;
        }

        return false;
    }
}
