<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao\Interfaces;

interface ComposicaoBaseCalculoInterface
{
    public function setRetencao($retencao);
    public function getComposicao();
}
