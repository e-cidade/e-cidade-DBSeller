<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Enums;

use ECidade\Enum\Enum;

class TipoVariavelEnum extends Enum
{
    const VARCHAR = 'varchar';
    const INT = 'int4';
    const FLOAT = 'float8';
    const DATE = 'date';
    const BOOL = 'bool';
    const SELECT = 'select';

    /**
     * @return string
     */
    public function name()
    {
        $data = [
            self::VARCHAR => 'Texto livre',
            self::INT => 'Número sem decimais',
            self::FLOAT => 'Número com decimais',
            self::DATE => 'Data',
            self::BOOL => 'Lógico',
            self::SELECT => 'Select'
        ];

        return $data[$this->getValue()];
    }
}
