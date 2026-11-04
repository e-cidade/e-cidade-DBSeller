<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use cl_termo;
use db_utils;

class DescontoTermoParcelamentoService
{

    /**
     * @param integer|null $iParcelamento
     */
    public function buscaPercentuaisDescontosDepoisLancamento($iParcelamento)
    {
        $oDaoTermo = new cl_termo();
        $sqlBuscaTermo = $oDaoTermo->sql_query_file(
            $iParcelamento,
            'v07_perjur as percentualdescontojuros,v07_permul as percentualdescontomultas',
            'v07_parcel',
            "v07_parcel = $iParcelamento"
        );
        $rsBuscaTermo = db_query($sqlBuscaTermo);

        if ($rsBuscaTermo && pg_num_rows($rsBuscaTermo) >= 1) {
            return db_utils::getCollectionByRecord($rsBuscaTermo)[0];
        } else {
            return null;
        }
    }
}
