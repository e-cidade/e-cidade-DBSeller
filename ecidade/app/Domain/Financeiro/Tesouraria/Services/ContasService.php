<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Financeiro\Tesouraria\Models\Saltes;

class ContasService
{

    public function get($filtros)
    {
        $contas = Saltes::query();

        if (!empty($filtros['comContrapartida'])) {
            $contas->with('contaContrapartida.conta');
        }

        if (!empty($filtros['comContaExtra'])) {
            $contas->with('contaExtra.conta');
        }

        if (!empty($filtros['comReduzidos'])) {
            $contas->join('contabilidade.conplanoreduz', 'c61_reduz', '=', 'k13_conta')
                ->join('contabilidade.conplano', function ($join) {
                    $join->on('c60_codcon', '=', 'c61_codcon')
                        ->on('c60_anousu', '=', 'c61_anousu');
                })
                ->join('orcamento.fonterecurso', 'orctiporec_id', '=', 'c61_codigo')
                ->join('orcamento.orctiporec', 'o15_codigo', '=', 'orctiporec_id')
                ->where('exercicio', $filtros['DB_anousu'])
                ->where('c61_anousu', $filtros['DB_anousu'])
                ->where('c61_instit', $filtros['DB_instit']);
        }

        $contas->orderBy('k13_descr');
        return $contas->get();
    }
}
