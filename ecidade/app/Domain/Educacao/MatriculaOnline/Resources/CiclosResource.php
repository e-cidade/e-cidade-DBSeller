<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\Ciclo;
use App\Domain\Educacao\Secretaria\Resources\EnsinoResource;
use Carbon\Carbon;

class CiclosResource
{
    public static function toResponse(Ciclo $ciclo)
    {
        return (object) [
            'codigo' => $ciclo->mo09_codigo,
            'isAtivo' => $ciclo->mo09_status,
            'dataCadastro' => $ciclo->mo09_dtcad->format('m/d/Y'),
            'nome' =>  $ciclo->mo09_descricao,
            'sigla' => $ciclo->mo09_sigla,
            'isEJA' => $ciclo->mo09_eja,
            'ensinos' => $ciclo->ciclosEnsino->map(function ($cicloEnsino) {
                return EnsinoResource::toResponse($cicloEnsino->ensino);
            })
        ];
    }

    public static function toArrayModel($ciclo)
    {
        return [
            'mo09_codigo' => isset($ciclo['codigo']) ? $ciclo['codigo'] : null,
            'mo09_status' => $ciclo['isAtivo'] == 1,
            'mo09_dtcad' => Carbon::createFromFormat('d/m/Y', $ciclo['dataCadastro'])->format('Y-m-d'),
            'mo09_descricao' => $ciclo['nome'],
            'mo09_sigla' =>  $ciclo['sigla'],
            'mo09_eja' => $ciclo['isEJA'] == 1,
            'ensinos' => $ciclo['ensinos']
        ];
    }
}
