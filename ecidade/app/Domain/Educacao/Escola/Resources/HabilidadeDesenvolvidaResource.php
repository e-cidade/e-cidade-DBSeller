<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\HabilidadeDesenvolvida;

class HabilidadeDesenvolvidaResource
{
    public static function toResponse(HabilidadeDesenvolvida $habilidade)
    {
        return (object) [
            'codigo' => $habilidade->ed156_codigo,
            'conteudoDesenvolvido' => $habilidade->ed156_diario_classe_bncc,
            'disciplina' => $habilidade->ed156_bnccdisciplinas,
            'habilidade' => $habilidade->ed156_habilidade
        ];
    }

    public static function toArrayModel(array $habilidade)
    {
        return [
            'ed156_codigo' => isset($habilidade['codigo']) ? $habilidade['codigo'] : null,
            'ed156_diario_classe_bncc' => $habilidade['conteudoDesenvolvido'],
            'ed156_bnccdisciplinas' => $habilidade['disciplina'],
            'ed156_habilidade' => $habilidade['habilidade']
        ];
    }
}
