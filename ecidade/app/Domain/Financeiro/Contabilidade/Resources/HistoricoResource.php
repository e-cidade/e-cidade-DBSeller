<?php

namespace App\Domain\Financeiro\Contabilidade\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class HistoricoResource
{
    public static function toArray(LengthAwarePaginator $dados)
    {
        $historicos = [];
        foreach ($dados as $dado) {
            $historicos[] = (object)[
                "codigo" => $dado->c50_codhist,
                "nome" => $dado->c50_descr,
                "apresentar" => $dado->apresentar
            ];
        }
        return (object)[
            'totalRegistros' => $dados->total(),
            'historicos' => $historicos
        ];
    }
}
