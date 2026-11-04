<?php

namespace ECidade\Enum\Saude\Farmacia;

use ECidade\Enum\Enum;

class TipoOperacaoBnafarEnum extends Enum
{
    const INCLUSAO = 1;
    const ALTERACAO = 2;
    const EXCLUSAO = 3;

    public function name()
    {
        $data = [
            self::INCLUSAO => 'INCLUSÃO',
            self::ALTERACAO => 'ALTERAÇÃO',
            self::EXCLUSAO => 'EXCLUSÃO'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Tipo de operação inválida.');
        }

        return $data[$this->getValue()];
    }
}
