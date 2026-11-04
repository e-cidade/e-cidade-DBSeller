<?php

namespace App\Domain\Educacao\MatriculaOnline\Enums;

use ECidade\Enum\Enum;
use Exception;

class TiposDocumentosEnum extends Enum
{
    const LEGISLACAO = 1;
    const TERMOS_USO = 2;
    const QUADRO_VAGAS = 3;
    const MANUAL = 4;
    const LEVANTAMENTO_DADOS = 5;
    /**
     * @return string
     * @throws Exception
     */
    public function name()
    {
        $data = array(
            self::LEGISLACAO => "Legislação",
            self::TERMOS_USO => "Termos de Uso",
            self::QUADRO_VAGAS => "Quadro de Vagas",
            self::MANUAL => "Manual",
            self::LEVANTAMENTO_DADOS => "Levantamento de Dados",
        );

        if (empty($data[$this->getValue()])) {
            throw new Exception('Tipo não encontrado.');
        }

        return $data[$this->getValue()];
    }
}
