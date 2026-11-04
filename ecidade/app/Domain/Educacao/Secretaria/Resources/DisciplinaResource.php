<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\Disciplina;
use App\Domain\Educacao\Escola\Resources\AreaConhecimentoResource;

class DisciplinaResource
{
    public static function toResponse(Disciplina $disciplina)
    {
        return (object) [
            'codigo' => $disciplina->ed232_i_codigo,
            'nome' => trim($disciplina->ed232_c_descr),
            'abreviatura' => $disciplina->ed232_c_abrev,
            'areaConhecimento' => AreaConhecimentoResource::toResponse($disciplina->areaConhecimento),
            'descricaoCompleta' => trim($disciplina->ed232_c_descrcompleta),
            "cor" => trim($disciplina->ed232_corhtml)
        ];
    }
}
