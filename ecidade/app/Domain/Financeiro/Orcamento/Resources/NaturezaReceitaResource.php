<?php

namespace App\Domain\Financeiro\Orcamento\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class NaturezaReceitaResource
{

    public static function toArray(LengthAwarePaginator $dados)
    {
        $naturezas = [];
        foreach ($dados as $dado) {
            $naturezas[] = (object)[
                "codigo" => $dado->o57_codfon,
                "exercicio" => $dado->o57_anousu,
                "estrutural" => $dado->o57_fonte,
                "descricao" => $dado->o57_descr,
                "apresentar" => sprintf('%s - %s', $dado->o57_fonte, $dado->o57_descr)
            ];
        }

        return (object)[
            'totalRegistros' => $dados->total(),
            'naturezas' => $naturezas
        ];
    }
}
