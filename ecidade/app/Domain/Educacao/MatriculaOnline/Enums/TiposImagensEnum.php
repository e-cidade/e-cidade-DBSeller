<?php

namespace App\Domain\Educacao\MatriculaOnline\Enums;

use ECidade\Enum\Enum;

class TiposImagensEnum extends Enum
{
    const TOPO = 1;
    const ESQUERDA = 2;
    const DIREITA = 3;
    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::TOPO => "Imagem Topo",
            self::ESQUERDA => "Imagem Esquerda",
            self::DIREITA => "Imagem Direita",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
