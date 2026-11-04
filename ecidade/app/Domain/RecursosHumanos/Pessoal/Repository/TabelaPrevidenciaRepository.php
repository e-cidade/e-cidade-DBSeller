<?php

namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\RecursosHumanos\Pessoal\Model\Previdencia\TabelaPrevidencia;

class TabelaPrevidenciaRepository extends BaseRepository
{
    public static function getTabelasPrevidencia($codigoInstituicao, $ano, $mes)
    {
        return TabelaPrevidencia::select('r33_codtab', 'r33_nome')
            ->distinct()
            ->where("r33_instit", "=", $codigoInstituicao)
            ->where("r33_anousu", "=", $ano)
            ->where("r33_mesusu", "=", $mes)
            ->where("r33_codtab", ">", 2)
            ->get();
    }
}
