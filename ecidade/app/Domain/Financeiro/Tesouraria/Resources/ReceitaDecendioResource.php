<?php

namespace App\Domain\Financeiro\Tesouraria\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class ReceitaDecendioResource
{
    public static function toArray($dados)
    {
        $receitas = [];
        foreach ($dados as $dado) {
            $hash = $dado->o70_codrec;
            if (!array_key_exists($hash, $receitas)) {
                $receitas[$hash] = self::toStdClass($dado);
            }
        }

        if ($dados instanceof LengthAwarePaginator) {
            return (object)[
                'totalRegistros' => $dados->total(),
                'receitas' => $receitas
            ];
        }

        return $receitas;
    }

    /**
     * @param $dado
     * @return object
     */
    public static function toStdClass($dado)
    {
        return (object)[
            "exercicio" => $dado->o70_anousu,
            "reduzido" => $dado->o70_codrec,
            "instituicao" => $dado->o70_instit,
            "id_recurso" => $dado->o70_codigo,
            "estrutural" => $dado->o57_fonte,
            "descricao" => $dado->o57_descr,
            "configurada" => !empty($dado->k224_tipodesdobramento),
            "siconfi" => $dado->codigo_siconfi,
            "complemento" => $dado->o15_complemento
        ];
    }
}
