<?php

namespace App\Domain\Configuracao\RelarorioLegal\Services;

use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RelatoriosLegaisService
{
    /**
     * @return Collection
     */
    public function relatoriosLRF()
    {
        return Relatorio::query()->relatoriosLrf()
            ->orderBy('o42_codparrel', 'desc')
            ->get();
    }

    /**
     * @param array $filtros
     * @return Relatorio[]
     */
    public function getByFilters(array $filtros)
    {
        return Relatorio::query()
            ->when(!empty($filtros['codigo']), function (Builder $query) use ($filtros) {
                $query->where('o42_codparrel', $filtros['codigo']);
            })
            ->when(!empty($filtros['codigos']), function (Builder $query) use ($filtros) {
                $query->whereIn('o42_codparrel', $filtros['codigos']);
            })
            ->when(!empty($filtros['with']), function (Builder $query) use ($filtros) {
                foreach ($filtros['with'] as $relation) {
                    $query->with($relation);
                }
            })
            ->get();
    }
}
