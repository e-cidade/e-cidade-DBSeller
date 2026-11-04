<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Models\Receita;
use App\Domain\Financeiro\Orcamento\Repositories\ReceitaRepository;

class ReceitaService
{
    protected $modelClass = Receita::class;

    public function get(array $filtros)
    {

        $orderBy = $this->getOrder($filtros);
        return (new ReceitaRepository())->getByFilters($filtros, $orderBy);
    }

    private function getOrder(array &$filtros)
    {
        $defaultOrderm = ['o57_fonte', 'o70_orcorgao', 'o70_orcunidade'];
        if (empty($filtros['sortField'])) {
            return $defaultOrderm;
        }

        $mapSort = [
            "reduzido" => "o70_codrec",
            "naturezaReceita" => "o57_fonte",
            "cp" => "o70_concarpeculiar",
            "recurso" => "o70_codigo",
            "saldoInicial" => "o70_valor",
        ];

        $filtros['sortField'] = $mapSort[$filtros['sortField']];
        return [];
    }
}
