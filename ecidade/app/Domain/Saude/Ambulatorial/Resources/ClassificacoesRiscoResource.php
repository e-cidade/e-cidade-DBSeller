<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use App\Domain\Saude\Ambulatorial\Models\ClassificacaoRisco;
use Illuminate\Database\Eloquent\Collection;

class ClassificacoesRiscoResource
{
    /**
     * @param Collection $dados
     * @return array
     */
    public static function toArray(Collection $dados)
    {
        $retorno = [];

        foreach ($dados as $dado) {
            $retorno[] = self::toObject($dado);
        }

        return $retorno;
    }

    /**
     * @param ClassificacaoRisco $classificacoesDeRisco
     * @return object
     */
    public static function toObject(ClassificacaoRisco $classificacoesDeRisco)
    {
        return (object)[
            'codigo' => $classificacoesDeRisco->sd78_codigo,
            'descricao' => $classificacoesDeRisco->sd78_descricao,
        ];
    }
}
