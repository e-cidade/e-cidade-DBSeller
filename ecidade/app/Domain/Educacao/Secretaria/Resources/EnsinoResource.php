<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\Ensino;

class EnsinoResource
{
    public static function toResponse(Ensino $ensino)
    {
        return (object) [
            'codigo' => $ensino->ed10_i_codigo,
            'tipoEnsino' => $ensino->ed10_i_tipoensino,
            'grauEnsino' => $ensino->ed10_i_grauensino,
            'descricao' => trim($ensino->ed10_c_descr),
            'abreviatura' => $ensino->ed10_c_abrev,
            'mediacaoPedagogica' => $ensino->ed10_mediacaodidaticopedagogica,
            'ordem' => $ensino->ed10_ordem,
            'tipoBncc' =>  $ensino->ed10_tipo
        ];
    }
}
