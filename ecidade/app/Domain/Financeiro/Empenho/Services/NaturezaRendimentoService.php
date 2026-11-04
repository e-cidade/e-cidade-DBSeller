<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Financeiro\Empenho\Models\NaturezaRendimento;
use Illuminate\Support\Facades\DB;

class NaturezaRendimentoService
{
    public function getNaturezasPorGrupo($grupo)
    {
        $naturezas = NaturezaRendimento::select('e167_sequencial')
            ->selectRaw("e167_codigo || ' - ' || e167_descricao as descricao")
            ->where(DB::Raw('e167_codigo::char(2)'), $grupo)
            ->get();

        return $naturezas;
    }

    public function getGrupos()
    {
        $grupos = NaturezaRendimento::selectRaw('e167_codigo::char(2) as grupo')
            ->distinct()
            ->orderBy('grupo')
            ->whereRaw("e167_codigo::char(2) <> '10'")
            ->get();
        return $grupos;
    }

    public function getNaturezas($filtros)
    {
        $naturezas = $naturezas = NaturezaRendimento::select('e167_sequencial')
        ->selectRaw("e167_codigo || ' - ' || e167_descricao as descricao")
        ->when($filtros->declarante, function ($q) use ($filtros) {
            $q->where('e167_declarante', 'ilike', "%$filtros->declarante%");
        })
        ->whereRaw("e167_codigo::char(2) <> '10'")
        ->get();

        return $naturezas;
    }
}
