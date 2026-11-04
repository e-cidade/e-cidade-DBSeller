<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao\Factories;

use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR2010;
use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR2055;
use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4010;
use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4020;
use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4040;
use BusinessException;

class RetencaoFactory
{
    const R2010 = 'r2010';
    const R2055 = 'r2055';
    const R4010 = 'r4010';
    const R4020 = 'r4020';
    const R4040 = 'r4040';

    public static function getInstance($evento)
    {
        switch ($evento) {
            case self::R2010:
                return new RetencaoR2010;
            case self::R2055:
                return new RetencaoR2055;
            case self::R4010:
                return new RetencaoR4010;
            case self::R4020:
                return new RetencaoR4020;
            case self::R4040:
                return new RetencaoR4040;
            default:
                throw new BusinessException('Evento não Encontrado');
                break;
        }
    }
}
