<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Secretaria\Models\UnidadeCurricular;

class UnidadeCurricularResource
{
    public static function toResponse(UnidadeCurricular $unidade)
    {
        return (object) [
            'codigo' => $unidade->ed199_id,
            'nome' => trim($unidade->ed199_descricao)
        ];
    }
}
