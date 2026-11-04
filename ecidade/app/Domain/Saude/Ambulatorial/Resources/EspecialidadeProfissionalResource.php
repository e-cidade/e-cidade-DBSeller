<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

use App\Domain\Saude\Ambulatorial\Models\EspecialidadeProfissional;
use Illuminate\Support\Collection;

class EspecialidadeProfissionalResource
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
     * @param EspecialidadeProfissional $especialidadeProfissionais
     * @return object
     */
    public static function toObject(EspecialidadeProfissional $especialidadeProfissionais)
    {
        return (object)[
            'codigo' => $especialidadeProfissionais->sd27_i_codigo,
            'nome' => $especialidadeProfissionais->profissionalUnidade->profissional->cgm->z01_nome,
        ];
    }
}
