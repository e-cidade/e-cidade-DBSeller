<?php

namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\CensoCursoProfissionalizante;
use App\Domain\Educacao\Escola\Models\CursoEdu;

class CursoResource
{
    public static function toResponse(CursoEdu $curso)
    {
        return (object) [
            'codigo' => $curso->ed29_i_codigo,
            'ensino' => EnsinoResource::toResponse($curso->ensino),
            'nome' => trim($curso->ed29_c_descr),
            'incluiNoHistorico' => $curso->ed29_c_historico === 'S' ? true : false,
            'habilitaAprovParcial' => $curso->ed29_c_historico === 1 ? true : false,
            'isAtivo' => $curso->ed29_ativo,
            'cursoProfissionalizante' => CensoCursoProfissionalizante::find($curso->ed29_censocursoprofiss),
            'cursoEscola' => $curso->ed71_i_codigo
        ];
    }
}
