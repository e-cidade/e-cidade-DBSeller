<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\RedeOrigem;

class RedeOrigemResource
{
    public static function toResponse(RedeOrigem $rede)
    {
        return (object) [
            'codigo' => $rede->mo05_codigo,
            'descricao' => $rede->mo05_descr
        ];
    }
}
