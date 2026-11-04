<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use App\Domain\Saude\Ambulatorial\Models\Unidade;

class UnidadeResource
{
    public static function toResponse(Unidade $unidade)
    {
        return (object)[
            'id' => $unidade->sd02_i_codigo,
            'descricao' => $unidade->departamento->descrdepto,
            'cnes' => $unidade->sd02_v_cnes
        ];
    }
}
