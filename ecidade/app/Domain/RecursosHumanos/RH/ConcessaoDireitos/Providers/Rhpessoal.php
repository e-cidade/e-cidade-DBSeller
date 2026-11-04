<?php

namespace App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Providers;

use App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Models\RhPessoal as ModelsRhPessoal;
use Illuminate\Support\Facades\DB;

class Rhpessoal
{
    public static function matriculas($inst, $DB_datausu, $selecao, $matricula)
    {
        $rhpessoal = ModelsRhPessoal::select('rh01_regist')
            ->join('rhpessoalmov', 'rh02_regist', '=', 'rh01_regist')
            ->leftJoin('rhpesrescisao', 'rh02_seqpes', '=', 'rh05_seqpes')
            ->whereNull('rh05_seqpes');

        $where = [
            ['rh02_anousu', date('Y', ($DB_datausu))],
            ['rh02_mesusu', date('m', ($DB_datausu))],
            ['rh02_instit', $inst]
        ];

        if ($matricula != null) {
            array_push($where, ['rh01_regist', $matricula]);
        }

        $rhpessoal->where($where);

        if ($selecao != null) {
            $rhpessoal->whereRaw($selecao);
        };
        return  $rhpessoal->get();
    }

    public static function verificarMatriculaSelecao($selecao, $matricula)
    {
        $rhpessoal = ModelsRhPessoal::select('rh01_regist')
            ->join('rhpessoalmov', 'rh02_regist', '=', 'rh01_regist')
            ->where('rh01_regist', $matricula)->groupby('rh01_regist')->distinct();

        if ($selecao != null) {
            $rhpessoal->whereRaw($selecao);
        }

        if (count($rhpessoal->get()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function admissrecis($inst, $matricula)
    {
        $rhpessoalmov = DB::select(DB::raw("
        select rh01_admiss, rh05_recis
        from rhpessoal
        inner join rhpessoalmov on
            rh02_regist = rh01_regist
            and rh02_anousu = fc_anofolha(" . $inst . ")
            and rh02_mesusu = fc_mesfolha(" . $inst . ")
        left join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
        where rh01_regist = " . $matricula));
        return $rhpessoalmov;
    }
}
