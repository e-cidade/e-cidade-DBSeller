<?php

namespace App\Domain\Patrimonial\Material\Resources;

use App\Domain\Patrimonial\Material\Models\Fabricante;

class FabricantesResource
{
    public static function toResponse(Fabricante $fabricante)
    {
        return (object)[
            'id' => $fabricante->m76_sequencial,
            'nome' => $fabricante->m76_nome,
            'cnpj' => $fabricante->cgm ? $fabricante->cgm->z01_cgccpf : ''
        ];
    }
}
