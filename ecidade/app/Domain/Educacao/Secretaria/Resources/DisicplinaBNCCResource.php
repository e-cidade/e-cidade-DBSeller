<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\DisciplinaBNCC;

class DisicplinaBNCCResource
{
    public static function toResponse(DisciplinaBNCC $disciplina)
    {
        return (object) [
            'codigo' => $disciplina->ed149_sequencial,
            'nome' => trim($disciplina->ed149_nome),
            'sigla' => $disciplina->ed149_sigla,
            'areaConhecimento' => trim($disciplina->ed149_area_conhecimento),
            'ensino' => trim($disciplina->ed149_ensino)
        ];
    }
}
