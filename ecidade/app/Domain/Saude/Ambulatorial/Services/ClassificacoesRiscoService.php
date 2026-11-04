<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Models\ClassificacaoRisco;

class ClassificacoesRiscoService
{
    /**
     * @return ClassificacaoRisco[]
     */
    public function recuperaClassificacoes()
    {
        return ClassificacaoRisco::all();
    }
}
