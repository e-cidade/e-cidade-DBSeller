<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use Illuminate\Support\Facades\DB;

class EncerramentoPeriodoContabilService
{

    public static function ultimaData($instituicao, $exercicio = null)
    {
        $encerramento = DB::table('condataconf')
            ->where('c99_instit', '=', $instituicao)
            ->when(!is_null($exercicio), function ($builder) use ($exercicio) {
                $builder->where('c99_anousu', '=', $exercicio);
            })
            ->orderBy('c99_anousu', 'desc')
            ->first();

        if (is_null($encerramento)) {
            return null;
        };

        return $encerramento->c99_data;
    }
}
