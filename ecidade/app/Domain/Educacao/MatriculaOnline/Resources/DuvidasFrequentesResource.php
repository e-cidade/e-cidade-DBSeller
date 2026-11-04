<?php

namespace App\Domain\Educacao\MatriculaOnline\Resources;

use App\Domain\Educacao\MatriculaOnline\Models\DuvidasFrequentes;
use Tests\Unit\Std\DBStringTest;

class DuvidasFrequentesResource
{
    public static function toResponse(DuvidasFrequentes $duvida)
    {
        return (object) [
            'codigo' => $duvida->mo15_id,
            'pergunta' => $duvida->mo15_pergunta,
            'respostas' => $duvida->respostas->map(function ($resposta) {
                return $resposta->mo25_resposta;
            }),
            'ativa' =>  $duvida->mo15_ativo,
            'ordem' => $duvida->mo15_ordem
        ];
    }

    public static function toArrayModel($duvida)
    {
        return [
            'mo15_id' => isset($duvida['codigo']) ? $duvida['codigo'] : null,
            'mo15_pergunta' => trim($duvida['pergunta']),
            'mo15_respostas' => $duvida['respostas'],
            'mo15_ativo' => $duvida['ativa'],
            'mo15_ordem' => $duvida['ordem'],
            'respostas' => $duvida['respostas']
        ];
    }
}
