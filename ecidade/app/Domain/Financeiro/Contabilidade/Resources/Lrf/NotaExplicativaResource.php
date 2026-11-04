<?php

namespace App\Domain\Financeiro\Contabilidade\Resources\Lrf;

class NotaExplicativaResource
{
    public static function toArray($dados)
    {
        $retornar = [];
        foreach ($dados as $dado) {
            $obj = (object)[
                "codigo" => $dado->o42_sequencial,
                "exercicio" => $dado->o42_anousu,
                "relatorio_id" => $dado->o42_codparrel,
                "instituicao_id" => $dado->o42_instit,
                "periodo_id" => $dado->o42_periodo,
                "nota" => $dado->o42_nota,
                "fonte" => $dado->o42_fonte,
                "notaSize" => $dado->o42_tamanhofontenota,
                "fonteSize" => $dado->o42_tamanhofontedados,
            ];

            if (isset($dado->o114_sequencial)) {
                $obj->periodo = (object)[
                    'codigo' => $dado->o114_sequencial,
                    'descricao' => $dado->o114_descricao,
                ];
            }
            $retornar[] = $obj;
        }
        return $retornar;
    }
}
