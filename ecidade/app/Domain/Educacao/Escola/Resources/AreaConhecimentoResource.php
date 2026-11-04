<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\AreaConhecimento;

class AreaConhecimentoResource
{
    public static function toResponse(AreaConhecimento $area)
    {
        return (object) [
            'codigo' => $area->ed293_sequencial,
            'nome' => $area->ed293_descr,
            'isAtivo' => $area->ed293_ativo === 1 ? true : false
        ];
    }
}
