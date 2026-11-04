<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\CamposOpcionais;
use App\Domain\Educacao\MatriculaOnline\Models\RedeOrigem;

class CamposOpicionaisResource
{
    public static function toResponse(CamposOpcionais $campo)
    {
        return (object) [
            'codigo' => $campo->mo42_codigo,
            'descricao' => $campo->mo42_campo,
            'apresenta' => $campo->mo42_apresenta,
            'obrigatorio' => $campo->mo42_obrigatorio
        ];
    }
}
