<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\Ensino;
use App\Domain\Educacao\Secretaria\Models\TiposIntrumentosAvaliativos;

class TiposInstrumentosAvaliativosResource
{
    public static function toResponse(TiposIntrumentosAvaliativos $instrumentos)
    {
        return (object) [
            'codigo' => $instrumentos->ed201_id,
            'nome' => trim($instrumentos->ed201_descricao),
            'ensinos' =>  Ensino::whereIn('ed10_i_codigo', $instrumentos->ed201_ensinos)
                ->get()->map(function ($ensino) {
                    return EnsinoResource::toResponse($ensino);
                }),
            'ativo' =>  $instrumentos->ed201_ativo
        ];
    }

    public static function toArrayModel($parametros)
    {
        $insturmento = (object) $parametros;
        return [
            'ed201_id' => isset($insturmento->codigo) ? $insturmento->codigo : null,
            'ed201_descricao' => trim($insturmento->nome),
            'ed201_ensinos' => $insturmento->ensinos,
            'ed201_ativo' => $insturmento->ativo == 1
        ];
    }
}
