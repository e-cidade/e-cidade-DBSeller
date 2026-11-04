<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Repositories\DotacaoRepository;

class DotacaoService
{
    public function get($filtros)
    {
        $orderBy = $this->getSort($filtros);
        return (new DotacaoRepository())->getByFilters($filtros, $orderBy);
    }

    private function getSort(&$filtros)
    {
        $defaultOrderm = [
            'o58_orgao',
            'o58_unidade',
            'o58_funcao',
            'o58_subfuncao',
            'o58_programa',
            'o58_projativ',
            'o58_codele',
            'o58_codigo'
        ];
        if (empty($filtros['sortField'])) {
            return $defaultOrderm;
        }

        $mapSort = [
            "reduzido" => "o58_coddot",
            "projeto" => "o58_projativ",
            "elemento" => "o56_elemento",
            "recurso" => "o58_codigo",
            "saldoInicial" => "o58_valor",
        ];

        $filtros['sortField'] = $mapSort[$filtros['sortField']];
        return [];
    }
}
