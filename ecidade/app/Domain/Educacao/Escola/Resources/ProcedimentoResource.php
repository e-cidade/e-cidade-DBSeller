<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Procedimento;

class ProcedimentoResource
{
    public static function toResponse(Procedimento $procedimento)
    {
        return (object) [
            'codigo' => $procedimento->ed40_i_codigo,
            'formaAvaliacao' => $procedimento->ed40_i_formaavaliacao,
            'nome' => trim($procedimento->ed40_c_descr),
            'percentualFrequencia' => $procedimento->ed40_i_percfreq,
            'controleFrequencia' => $procedimento->ed40_c_contrfreqmpd,
            'calculoFrequencia' => $procedimento->ed40_i_calcfreq,
            'desativado' => $procedimento->ed40_desativado
        ];
    }
}
