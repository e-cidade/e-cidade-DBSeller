<?php

namespace App\Domain\Educacao\Escola\Enums;

use ECidade\Enum\Enum;

class ControleFrequenciaEnum extends Enum
{
    const GLOBALIZADA = 'G';
    const INDIVIDUAL = 'I';

    /**
     * @return string
     * @throws Exception
     */
    public function descricao()
    {
        $data = array(
            self::GLOBALIZADA => "GLOBALIZADA",
            self::INDIVIDUAL => "INDIVIDUAL",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Controle não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
