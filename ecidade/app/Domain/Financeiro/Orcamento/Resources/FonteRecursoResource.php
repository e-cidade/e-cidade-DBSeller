<?php

namespace App\Domain\Financeiro\Orcamento\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class FonteRecursoResource
{
    public static function toArray(LengthAwarePaginator $dados)
    {
        $recursos = [];
        foreach ($dados as $recurso) {
            if (!array_key_exists($recurso->id, $recursos)) {
                $recursos[] = self::toObject($recurso);
            }
        }
        return (object)[
            'totalRegistros' => $dados->total(),
            'recursos' => $recursos
        ];
    }

    public static function toObject($recurso)
    {
        return (object)[
            "id" => $recurso->id,
            "orctiporec_id" => $recurso->orctiporec_id,
            "exercicio" => $recurso->exercicio,
            "siconfi" => $recurso->codigo_siconfi,
            "gestao" => $recurso->gestao,
            "subrecurso" => $recurso->o15_recurso,
            "complemento" => self::toComplemento($recurso),
            "classificacaofr_id" => $recurso->classificacaofr_id,
            "tipo_detalhamento" => $recurso->tipo_detalhamento,
            "descricao_fr" => $recurso->descricao,
            "descricao" => $recurso->o15_descr,
            "data_limite" => $recurso->o15_datalimite,
            "apresentacao" => sprintf(
                '%s - %s - %s - %s',
                $recurso->codigo_siconfi,
                $recurso->o15_recurso,
                $recurso->o200_sequencial,
                $recurso->o15_descr
            )
        ];
    }

    private static function toComplemento($recurso)
    {
        return (object)[
            "codigo" => $recurso->o200_sequencial,
            "descricao" => $recurso->o200_descricao,
            "msc" => $recurso->o200_msc,
            "tribunal" => $recurso->o200_tribunal,
        ];
    }
}
