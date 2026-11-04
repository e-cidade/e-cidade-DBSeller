<?php

namespace App\Domain\Tributario\Arrecadacao\Pix\Bancos;

use Exception;

class BancoFactory
{
    public static function getByBanco($banco = null)
    {
        if (empty($banco)) {
            throw new Exception("Nenhum banco configurado para emissão do pix");
        }

        switch ($banco) {
            case BancoDoBrasil::BANK_CODE:
                return new BancoDoBrasil();
                break;
            case Banrisul::BANK_CODE:
                return new Banrisul();
                    break;
            case Itau::BANK_CODE:
                return new Itau();
                break;
            default:
                throw new Exception("Banco não possui API Pix implementada");
                break;
        }
    }
}
