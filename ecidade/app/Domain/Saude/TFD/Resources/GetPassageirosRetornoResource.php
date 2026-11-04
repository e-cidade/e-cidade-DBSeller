<?php

namespace App\Domain\Saude\TFD\Resources;

use App\Domain\Saude\TFD\Models\PassageiroRetorno;
use Illuminate\Database\Eloquent\Collection;

class GetPassageirosRetornoResource
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
     * @param PassageiroRetorno $passageiro
     * @return object
     */
    public static function toObject(PassageiroRetorno $passageiro)
    {
        return (object)[
            'nome' => $passageiro->z01_v_nome,
            'cgs' => $passageiro->tf19_i_cgsund,
        ];
    }
}
