<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Repositories\NaturezaDespesaRepositoty;

class NaturezaDespesaService
{
    public function get(array $filtros)
    {
        return (new NaturezaDespesaRepositoty())->getByFilters($filtros, $this->getSort());
    }

    private function getSort()
    {
        return ['o56_elemento'];
    }
}
