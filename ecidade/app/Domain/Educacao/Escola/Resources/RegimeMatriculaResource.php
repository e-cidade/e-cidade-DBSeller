<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\DivisoesRegimeMatricula;
use App\Domain\Educacao\Escola\Models\RegimeMatricula;

class RegimeMatriculaResource
{
    public static function toResponse(RegimeMatricula $regime)
    {
        return (object) [
            'codigo' => $regime->ed218_i_codigo,
            'nome' => trim($regime->ed218_c_nome),
            'abreviatura' => $regime->ed218_c_abrev,
            'divisao' => $regime->ed218_c_divisao,
            'organizacaoTurma' => $regime->ed218_organizacaoturmam,
            'divisoes' => $regime->divisoes->map(function ($divisao) {
                return self::toResponseDivisao($divisao);
            })
        ];
    }

    public static function toResponseDivisao(DivisoesRegimeMatricula $divisao)
    {
        return (object) [
            'codigo' => $divisao->ed219_i_codigo,
            'nome' => trim($divisao->ed219_c_nome),
            'abreviatura' => $divisao->ed219_c_abrev,
            'ordenacao' => $divisao->ed219_i_ordenacao,
            'regimeMatricula' => $divisao->ed219_i_regimemat
        ];
    }
}
