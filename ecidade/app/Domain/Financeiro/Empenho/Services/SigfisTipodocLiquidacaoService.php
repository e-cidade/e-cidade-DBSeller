<?php

namespace App\Domain\Financeiro\Empenho\Services;

use BusinessException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SigfisTipodocLiquidacaoService
{

    /**
     * Tipo de documento de liquidacao de acordo com sigfis
     *
     * @return Collection[]|null
     */
    public function getTipos()
    {
        $aTipos = DB::table('sigfistipodocliquidacao')->get();

        if (!$aTipos) {
            throw new BusinessException("Erro ao buscar o tipo de documento sigfis");
        }

        return $aTipos;
    }
}
