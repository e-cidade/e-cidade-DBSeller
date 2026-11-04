<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\NecessidadeEspecial;

class NecessidadeEspecialResource
{
    public static function toResponse(NecessidadeEspecial $necessidade)
    {
        return (object) [
            'codigo' => $necessidade->ed48_i_codigo,
            'nome' => $necessidade->ed48_c_descr,
            'subdivisoes' => $necessidade->subdivisoes->map(function ($sub) {
                return (object) [
                    'codigo' => $sub->ed185_sequencial,
                    'nome' => $sub->ed185_descricao
                ];
            })
        ];
    }
}
