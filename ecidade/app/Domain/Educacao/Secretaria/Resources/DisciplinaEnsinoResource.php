<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\DisciplinaEnsino;

class DisciplinaEnsinoResource
{
    public static function toResponse(DisciplinaEnsino $disciplina)
    {
        return (object) [
            'codigo' => $disciplina->ed12_i_codigo,
            'ensino' => (object) [
                'codigo' => $disciplina->ensino->ed10_i_codigo,
                'nome' => $disciplina->ensino->ed10_c_descr
            ],
            'disciplina' => DisciplinaResource::toResponse($disciplina->disciplina),
            'matrizcurricular' => $disciplina->ed12_matrizcurricular
        ];
    }
}
