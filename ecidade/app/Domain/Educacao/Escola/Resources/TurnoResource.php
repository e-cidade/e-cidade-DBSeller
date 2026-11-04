<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Turno;

class TurnoResource
{
    public static function toResponse(Turno $turno)
    {
        return (object) [
            'codigo' => $turno->ed15_i_codigo,
            'ordem' => $turno->ed15_i_sequencia,
            'nome' => trim($turno->ed15_c_nome)
        ];
    }
}
