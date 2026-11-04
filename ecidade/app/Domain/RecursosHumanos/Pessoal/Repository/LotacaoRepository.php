<?php

namespace App\Domain\RecursosHumanos\Pessoal\Repository;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\RecursosHumanos\Pessoal\Model\RhLota;

class LotacaoRepository extends BaseRepository
{
    public static function getLotacoes($codigoInstituicao)
    {
        return RhLota::select('r70_codigo', 'r70_estrut', 'r70_descr')
            ->distinct()
            ->join('protocolo.cgm', 'cgm.z01_numcgm', '=', 'rhlota.r70_numcgm')
            ->join('configuracoes.db_estrutura', 'db_estrutura.db77_codestrut', '=', 'rhlota.r70_codestrut')
            ->join('contabilidade.concarpeculiar', 'concarpeculiar.c58_sequencial', '=', 'rhlota.r70_concarpeculiar')
            ->where("r70_instit", "=", $codigoInstituicao)
            ->orderBy('r70_codigo')
            ->get();
    }

    public static function getLotacao($codigoInstituicao, $codigo)
    {
        return RhLota::select('r70_codigo', 'r70_descr')
            ->distinct()
            ->join('protocolo.cgm', 'cgm.z01_numcgm', '=', 'rhlota.r70_numcgm')
            ->join('configuracoes.db_estrutura', 'db_estrutura.db77_codestrut', '=', 'rhlota.r70_codestrut')
            ->join('contabilidade.concarpeculiar', 'concarpeculiar.c58_sequencial', '=', 'rhlota.r70_concarpeculiar')
            ->where("r70_instit", "=", $codigoInstituicao)
            ->where("r70_codigo", "=", $codigo)
            ->orderBy('r70_codigo')
            ->first();
    }

    public static function getLotacoesAtivas($codigoInstituicao)
    {
        return RhLota::select('r70_codigo', 'r70_estrut', 'r70_descr')
            ->distinct()
            ->where("r70_instit", "=", $codigoInstituicao)
            ->where("r70_ativo", "=", true)
            ->orderBy('r70_codigo')
            ->get();
    }
}
