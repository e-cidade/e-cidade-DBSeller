<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use App\Domain\Saude\Ambulatorial\Models\SetorAmbulatorial;
use Illuminate\Database\Eloquent\Collection;

class SetoresAmbulatoriaisResource
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
     * @param SetorAmbulatorial $setorAmbulatorial
     * @return object
     */
    public static function toObject(SetorAmbulatorial $setorAmbulatorial)
    {
        return (object)[
            'codigo' => $setorAmbulatorial->sd91_codigo,
            'descricao' => $setorAmbulatorial->sd91_descricao,
        ];
    }
}
