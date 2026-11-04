<?php

namespace ECidade\Enum\Saude\Farmacia;

use ECidade\Enum\Enum;

class SituacaoProcessamentoBnafarEnum extends Enum
{
    const NA_FILA = 1;
    const EM_PROCESSAMENTO = 2;
    const CONCLUIDO = 3;
    const INCONSISTENTE = 4;
    const ERRO_INTERNO = 5;

    /**
     * @return string
     * @throws \Exception
     */
    public function name()
    {
        $data = [
            self::NA_FILA => 'NA FILA',
            self::EM_PROCESSAMENTO => 'EM PROCESSAMENTO',
            self::CONCLUIDO => 'CONCLUÍDO',
            self::INCONSISTENTE => 'INCONSISTENTE',
            self::ERRO_INTERNO => 'ERRO INTERNO'
        ];

        if (empty($data[$this->getValue()])) {
            throw new \Exception('Situação inválida.');
        }

        return $data[$this->getValue()];
    }
}
