<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\Views\TurmasRegenteView;
use Illuminate\Database\Eloquent\Collection;

class TurmasRegentesViewResource
{
    public static function toResponse(Collection $turmasRegentes, $periodos = false)
    {
        $resposta = [];
        foreach ($turmasRegentes as $registro) {
            if (!array_key_exists($registro->codigo_turma, $resposta)) {
                if ($periodos) {
                    if (trim($registro->turma->ed57_c_medfreq) === 'DIAS LETIVOS') {
                        continue;
                    }
                }
                $resposta[$registro->codigo_turma] =
                    TurmaResource::toResponse($registro->turma, ['etapa', 'turnoReferente']);
                $resposta[$registro->codigo_turma]->calendario =
                    CalendarioResource::toResponse($registro->calendarioTurma);
                $resposta[$registro->codigo_turma]->etapas = [];
            }
            if (!array_key_exists($registro->codigo_etapa, $resposta[$registro->codigo_turma]->etapas)) {
                $etapa = EtapaResource::toResponse($registro->etapa);
                $resposta[$registro->codigo_turma]->etapas[$registro->codigo_etapa] = $etapa;
                $resposta[$registro->codigo_turma]->etapas[$registro->codigo_etapa]->regencias = [];
            }
            $regencia = RegenciaResource::toResponse($registro->regencia);
            $resposta[$registro->codigo_turma]->etapas[$registro->codigo_etapa]->regencias[] = $regencia;
        }

        foreach ($resposta as $turma) {
            foreach ($turma->etapas as $etapa) {
                $etapa->regencias = array_values($etapa->regencias);
            }
            $turma->etapas = array_values($turma->etapas);
        }
        return array_values($resposta);
    }
}
