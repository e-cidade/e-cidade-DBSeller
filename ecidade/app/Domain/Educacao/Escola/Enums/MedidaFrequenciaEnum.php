<?php

namespace App\Domain\Educacao\Escola\Enums;

use ECidade\Enum\Enum;

class MedidaFrequenciaEnum extends Enum
{
    const DIA_LETIVO = 'D';
    const PERIODO = 'P';
    const DIA_LETIVO_EXTENSO = 'DIAS LETIVOS';

    const PERIODO_EXTENSO = 'PERIODOS';
    /**
     * @return string
     * @throws Exception
     */

    public function __construct($value)
    {
        $value = \DBString::removerCaracteresEspeciaisAcentos($value);
        parent::__construct($value);
    }

    public function descricao()
    {
        $data = array(
            self::DIA_LETIVO => "DIA LETIVO",
            self::PERIODO => "PERÍODO",
            self::PERIODO_EXTENSO => 'P',
            self::DIA_LETIVO_EXTENSO => 'D'
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Medida não encontrada.');
        }

        return $data[$this->getValue()];
    }
}
