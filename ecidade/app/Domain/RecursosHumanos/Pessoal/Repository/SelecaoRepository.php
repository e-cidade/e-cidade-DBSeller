<?php

namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\RecursosHumanos\Pessoal\Model\Selecao;

class SelecaoRepository extends BaseRepository
{
    public static function getSelecoes($codigoInstituicao)
    {
        return Selecao::select('r44_selec', 'r44_descr')
            ->distinct()
            ->where("r44_instit", "=", $codigoInstituicao)
            ->orderBy('r44_selec')
            ->get();
    }

    public static function getSelecao($codigoInstituicao, $codigo)
    {
        return Selecao::select('r44_selec', 'r44_descr')
            ->distinct()
            ->where("r44_instit", "=", $codigoInstituicao)
            ->where("r44_selec", "=", $codigo)
            ->orderBy('r44_selec')
            ->first();
    }
}
