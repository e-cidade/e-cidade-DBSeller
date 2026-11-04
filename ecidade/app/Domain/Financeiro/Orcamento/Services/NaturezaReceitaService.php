<?php

namespace App\Domain\Financeiro\Orcamento\Services;

use App\Domain\Financeiro\Orcamento\Repositories\NaturezaReceitaRepositoty;

class NaturezaReceitaService
{

    public function get(array $filtros)
    {
        return (new NaturezaReceitaRepositoty())->getByFilters($filtros, $this->getSort());
    }

    private function getSort()
    {
        return ['o57_fonte'];
    }
}
