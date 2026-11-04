<?php

namespace App\Domain\Educacao\Escola\Enums;

use ECidade\Enum\Enum;

class NacionalidadeEnum extends Enum
{
    const BRASILEIRA = 1;
    const BRASILEIRA_NATURALIZADO = 2;
    const ESTRANGEIRA = 3;

    /**
     * @return string
     * @throws Exception
     */
    public function descricao()
    {
        $data = array(
            self::BRASILEIRA => "BRASILEIRA",
            self::BRASILEIRA_NATURALIZADO => "BRASILEIRA NASCIDO NO EXTERIOR OU NATURALIZADO",
            self::ESTRANGEIRA => "ESTRANGEIRA",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Nacionalidade não encontrada.');
        }

        return $data[$this->getValue()];
    }
}
