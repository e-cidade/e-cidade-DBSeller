<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Enums;

use ECidade\Enum\Enum;

class TipoVisualizacaoRelatorioEnum extends Enum
{
    const USUARIO = 1;
    const DEPARTAMENTO = 2;
    const PUBLICO = 3;
    const CUBOS_BI = 4;
}
