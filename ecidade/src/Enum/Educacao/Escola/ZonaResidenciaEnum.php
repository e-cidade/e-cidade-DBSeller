<?php

namespace ECidade\Enum\Educacao\Escola;

use ECidade\Enum\Enum;
use Exception;

class ZonaResidenciaEnum extends Enum
{
    const URBANA = 1;
    const RURAL = 2;
    const URBANA_NORTE = 3;
    const URBANA_SUL = 4;
    const URBANA_LESTE = 5;
    const URBANA_OESTE = 6;


    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::URBANA => "Urbana",
            self::RURAL => "Rural",
            self::URBANA_NORTE => "Urbana/Norte",
            self::URBANA_SUL => "Urbana/Sul",
            self::URBANA_LESTE => "Urbana/Leste",
            self::URBANA_OESTE => "Urbana/Oeste",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Zona de residência não encontrada.');
        }

        return $data[$this->getValue()];
    }
}
