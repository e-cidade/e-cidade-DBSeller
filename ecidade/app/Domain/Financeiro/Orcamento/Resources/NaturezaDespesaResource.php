<?php

namespace App\Domain\Financeiro\Orcamento\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class NaturezaDespesaResource
{
    public static function toArray(LengthAwarePaginator $dados)
    {
        $elementos = [];
        foreach ($dados as $dado) {
            $elementos[] = (object)[
                "codigo" => $dado->o56_codele,
                "exercicio" => $dado->o56_anousu,
                "elemento" => $dado->o56_elemento,
                "descricao" => $dado->o56_descr,
                "apresentar" => sprintf('%s - %s', $dado->o56_elemento, $dado->o56_descr)
            ];
        }

        return (object)[
            'totalRegistros' => $dados->total(),
            'elementos' => $elementos
        ];
    }
}
