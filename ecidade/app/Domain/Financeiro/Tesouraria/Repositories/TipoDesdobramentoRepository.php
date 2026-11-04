<?php

namespace App\Domain\Financeiro\Tesouraria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use Illuminate\Database\Eloquent\Builder;

class TipoDesdobramentoRepository extends BaseRepository
{
    protected $modelClass = TipoDesdobramento::class;

    public function get()
    {
        return $this->newQuery()
            ->with('recursos')
            ->get();
    }

    public function getByFilters(array $filtros, $getSort)
    {
        return $this->newQuery()
            ->when(!empty($filtros['codigo']), function (Builder $query) use ($filtros) {
                $query->where('k222_codigo', $filtros['codigo']);
            })
            ->when(!empty($filtros['origem']), function (Builder $query) use ($filtros) {
                $query->where('k222_origem', $filtros['origem']);
            })
            ->when(!empty($filtros['descricao']), function (Builder $query) use ($filtros) {
                $query->where('k222_descricao', mb_strtoupper($filtros['descricao']));
            })
            ->with('recursos')
            ->get();
    }
}
