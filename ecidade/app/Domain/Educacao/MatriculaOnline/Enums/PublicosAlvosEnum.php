<?php

namespace App\Domain\Educacao\MatriculaOnline\Enums;

use ECidade\Enum\Enum;

class PublicosAlvosEnum extends Enum
{
    const PCD = 1;
    const REDE = 2;
    const FORA_REDE = 3;

    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::PCD => "Candidatos PCD",
            self::REDE => "Transferência na Rede",
            self::FORA_REDE => "Candidatos Fora da Rede"
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Public Alvo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
