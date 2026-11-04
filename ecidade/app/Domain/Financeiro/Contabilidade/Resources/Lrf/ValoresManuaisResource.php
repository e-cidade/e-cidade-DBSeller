<?php

namespace App\Domain\Financeiro\Contabilidade\Resources\Lrf;

class ValoresManuaisResource
{
    public static function toArray($dados)
    {
        $retornar = [];
        foreach ($dados as $dado) {
            $dtAlterado = null;
            if ($dado->updated_at) {
                $dtAlterado =$dado->updated_at->format('Y-m-d');
            }

            $retornar[] = (object)[
                "codigo" => $dado->c180_codigo,
                "relatorio_id" => $dado->c180_relatorio,
                "instituicao_id" => $dado->c180_instituicao,
                "linha" => $dado->c180_linha,
                "coluna" => $dado->c180_coluna,
                "exercicio" => $dado->c180_exercicio,
                "mes" => $dado->c180_mes,
                "valor" => $dado->c180_valor,
                "criado_em" => $dado->created_at->format('Y-m-d'),
                "alterado_em" => $dtAlterado,
            ];
        }
        return $retornar;
    }
}
