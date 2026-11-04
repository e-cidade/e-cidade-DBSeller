<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao\Factories;

use App\Domain\Integracoes\EFDReinf\Retencao\ValueObjects\Evento;
use App\Domain\Integracoes\EFDReinf\Services\ComposicaoBaseCalculoR4010;
use BusinessException;

final class ComposicaoBaseCalculoFactory
{
    public function create(Evento $evento)
    {
        switch ($evento->codigo()) {
            case 'R-4010':
                return new ComposicaoBaseCalculoR4010();
            default:
                throw new BusinessException("
                    Evento {$evento->codigo()} não possui
                    regra de composicao de base cadastrada.
                ");
                break;
        }
    }
}
