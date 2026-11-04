<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use Illuminate\Database\Eloquent\Collection;

class BuscaPacienteProntuarioResource
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
     * @param $paciente
     * @return object
     */
    public static function toObject($paciente)
    {
        return (object)[
            'codigo' => $paciente->z01_i_cgsund,
            'nome' => $paciente->z01_v_nome,
        ];
    }
}
