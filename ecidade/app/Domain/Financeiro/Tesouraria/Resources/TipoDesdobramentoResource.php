<?php

namespace App\Domain\Financeiro\Tesouraria\Resources;

use Illuminate\Database\Eloquent\Collection;

class TipoDesdobramentoResource
{
    /**
     * @param Collection $desdobramentos
     * @return array
     */
    public static function toArray(Collection $desdobramentos)
    {
        $dados = [];

        foreach ($desdobramentos as $desdobramento) {
            $dados[] = (object)[
                "codigo" => $desdobramento->k222_codigo,
                "descricao" => $desdobramento->k222_descricao,
                "percentual" => $desdobramento->k222_percentual,
                "recursos" => self::recursos($desdobramento->recursos)
            ];
        }
        return $dados;
    }

    /**
     * @param $recursos
     * @return array
     */
    private static function recursos($recursos)
    {
        $dados = [];
        foreach ($recursos as $recurso) {
            $dados[] = self::recursoToObject($recurso);
        }
        return $dados;
    }

    /**
     * @param $recurso
     * @return object
     */
    private static function recursoToObject($recurso)
    {
        return (object)[
            "orctiporec_id" => $recurso->o15_codigo,
            "exercicio" => $recurso->fonteRecurso->exercicio,
            "siconfi" => $recurso->fonteRecurso->codigo_siconfi,
            "gestao" => $recurso->fonteRecurso->gestao,
            "subrecurso" => $recurso->o15_recurso,
            "complemento" => self::toComplemento($recurso->complemento),
            "classificacaofr_id" => $recurso->fonteRecurso->classificacaofr_id,
            "tipo_detalhamento" => $recurso->fonteRecurso->tipo_detalhamento,
            "descricao_fr" => $recurso->fonteRecurso->descricao,
            "descricao" => $recurso->o15_descr,
            "data_limite" => $recurso->o15_datalimite,
            "apresentacao" => sprintf(
                '%s - %s - %s - %s',
                $recurso->fonteRecurso->codigo_siconfi,
                $recurso->o15_recurso,
                $recurso->complemento->o200_sequencial,
                $recurso->o15_descr
            )
        ];
    }

    /**
     * @param $complemento
     * @return object
     */
    private static function toComplemento($complemento)
    {
        return (object)[
            "codigo" => $complemento->o200_sequencial,
            "descricao" => $complemento->o200_descricao,
            "msc" => $complemento->o200_msc,
            "tribunal" => $complemento->o200_tribunal,
        ];
    }
}
