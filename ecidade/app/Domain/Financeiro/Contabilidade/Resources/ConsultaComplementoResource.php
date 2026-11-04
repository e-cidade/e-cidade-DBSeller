<?php

namespace App\Domain\Financeiro\Contabilidade\Resources;

use Illuminate\Database\Eloquent\Collection;

class ConsultaComplementoResource
{
    public static function toArray(Collection $dados)
    {
        $complementos = [];
        foreach ($dados as $dado) {
            if (!array_key_exists($dado->o200_sequencial, $complementos)) {
                $complementos[$dado->o200_sequencial] = self::toObject($dado);
            }
        }

        return $complementos;
    }

    public static function toObject($dado)
    {
        return (object)[
            "codigo" => $dado->o200_sequencial,
            "descricao" => $dado->o200_descricao,
            "apresentar" => "{$dado->o200_sequencial} - {$dado->o200_descricao}",
            "apresentaMSC" => $dado->o200_msc,
            "apresentaPAD" => $dado->o200_tribunal,
        ];
    }
}
