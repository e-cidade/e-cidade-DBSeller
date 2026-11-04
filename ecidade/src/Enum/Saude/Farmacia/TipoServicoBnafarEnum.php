<?php

namespace ECidade\Enum\Saude\Farmacia;

use ECidade\Enum\Enum;

class TipoServicoBnafarEnum extends Enum
{
    const ENTRADA = 1;
    const SAIDA = 2;
    const DISPENSACAO = 3;
    const POSICAO_ESTOQUE = 4;
    const AVALIACAO = 5;

    /**
     * @return string
     * @throws \Exception
     */
    public function name()
    {
        $data = [
            self::ENTRADA => 'ENTRADA',
            self::SAIDA => 'SAÍDA',
            self::DISPENSACAO => 'DISPENSAÇÃO',
            self::POSICAO_ESTOQUE => 'POSIÇÃO DO ESTOQUE',
            self::AVALIACAO => 'AVALIAÇÃO'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Tipo de serviço inválido.');
        }

        return $data[$this->getValue()];
    }
}
