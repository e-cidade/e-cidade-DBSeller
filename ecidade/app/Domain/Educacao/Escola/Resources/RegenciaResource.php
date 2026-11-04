<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Regencia;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaEnsinoResource;
use App\Domain\Educacao\Secretaria\Resources\DisciplinaResource;

class RegenciaResource
{
    public static function toResponse(Regencia $regencia)
    {
        return (object) [
            'codigo' => $regencia->ed59_i_codigo,
            'disciplina' => DisciplinaEnsinoResource::toResponse($regencia->disciplinaEnsino),
            'etapa' => EtapaResource::toResponse($regencia->etapa),
            'temGradeHorario' => $regencia->temGradeHorario,
            'procedimentoAvaliacao' => ProcedimentoResource::toResponse($regencia->procedimentoAvaliacao)
        ];
    }
}
