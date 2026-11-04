<?php

namespace ECidade\Enum\Saude\Ambulatorial;

use ECidade\Enum\Enum;
use Exception;

class RacaCorEnum extends Enum
{
    const BRANCA = '1';
    const PRETA = '2';
    const PARDA = '3';
    const AMARELA = '4';
    const INDIGENA = '5';
    const NAO_DECLARADA = '6';

    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = [
            self::BRANCA => 'BRANCA',
            self::PRETA => 'PRETA',
            self::PARDA => 'PARDA',
            self::AMARELA => 'AMARELA',
            self::INDIGENA => 'INDÍGENA',
            self::NAO_DECLARADA => 'NÃO DECLARADA'
        ];

        if (empty($data[$this->getValue()])) {
            throw new Exception('Opção de raça inválida.');
        }

        return $data[$this->getValue()];
    }

    public static function getValueByName($name)
    {
        $values = [
            self::BRANCA => ['BRANCA'],
            self::PRETA => ['PRETA'],
            self::PARDA => ['PARDA'],
            self::AMARELA => ['AMARELA'],
            self::INDIGENA => ['INDIGENA', 'INDÍGENA'],
            self::NAO_DECLARADA => ['NAO DECLARADA', 'NÃO DECLARADA']
        ];

        foreach ($values as $codigo => $descrRacas) {
            if (in_array($name, $descrRacas, true)) {
                return $codigo;
            }
        }

        return null;
    }
}
