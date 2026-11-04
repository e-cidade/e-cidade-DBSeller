<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Escola\Models\RegimeMatricula;
use App\Domain\Educacao\Secretaria\Resources\EnsinoResource;

class EtapaResource
{
    public static function toResponse(Etapa $etapa)
    {
        return (object) [
            'codigo' => $etapa->ed11_i_codigo,
            'ensino' => EnsinoResource::toResponse($etapa->ensino),
            'nome' => trim($etapa->ed11_c_descr),
            'abreviatura' => trim($etapa->ed11_c_abrev),
            'sequencia' => $etapa->ed11_i_sequencia,
            'codigoCenso' => $etapa->ed11_i_codcenso,
            'nomeRegimeMatricula' => trim($etapa->ed218_c_nome)
        ];
    }
}
