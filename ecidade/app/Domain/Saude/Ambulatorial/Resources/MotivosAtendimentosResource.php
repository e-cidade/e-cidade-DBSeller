<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use App\Domain\Saude\Ambulatorial\Models\MotivoAtendimento;
use Illuminate\Database\Eloquent\Collection;

class MotivosAtendimentosResource
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
     * @param MotivoAtendimento $motivosDeAtendimento
     * @return object
     */
    public static function toObject(MotivoAtendimento $motivosDeAtendimento)
    {
        return (object)[
            'codigo' => $motivosDeAtendimento->s144_i_codigo,
            'descricao' => $motivosDeAtendimento->s144_c_descr,
        ];
    }
}
