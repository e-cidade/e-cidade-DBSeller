<?php

namespace App\Domain\Financeiro\Empenho\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class EmpenhoResource
{
    public static function toDialog(LengthAwarePaginator $dados)
    {
        $empenhos = [];
        foreach ($dados as $dado) {
            $empenhos[] = (object)[
                "numemp" => $dado->e60_numemp,
                "numero" => $dado->e60_codemp,
                "exercicio" => $dado->e60_anousu,
                "numeroEmpenho" => sprintf('%s/%s', $dado->e60_codemp, $dado->e60_anousu),
                "dataEmissao" => $dado->e60_emiss,
                "cgm" => (object)[
                    "codigo" => $dado->e60_numcgm,
                    "nome" => $dado->z01_nome,
                ],
                "valorEmpenhado" => $dado->e60_vlremp,
                "valorAnulado" => $dado->e60_vlranu,
                "valorLiquidado" => $dado->e60_vlrliq,
                "valorPago" => $dado->e60_vlrpag,
                "saldoLiquido" => $dado->saldo_liquidado,
                "saldo" => $dado->saldo,
            ];
        }
        return (object)[
            'totalRegistros' => $dados->total(),
            'empenhos' => $empenhos
        ];
    }
}
