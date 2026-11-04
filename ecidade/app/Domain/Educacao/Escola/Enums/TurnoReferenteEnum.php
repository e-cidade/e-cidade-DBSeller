<?php

namespace App\Domain\Educacao\Escola\Enums;

use ECidade\Enum\Enum;

class TurnoReferenteEnum extends Enum
{
    const MANHA = 1;
    const TARDE = 2;
    const NOITE = 3;

    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::MANHA => "MANHA",
            self::TARDE => "TARDE",
            self::NOITE => "NOITE",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Turno não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
