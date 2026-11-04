<?php

namespace App\Domain\Financeiro\Orcamento\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Orcamento\Models\NaturezaDespesa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NaturezaDespesaRepositoty extends BaseRepository
{
    protected $modelClass = NaturezaDespesa::class;

    public function getByFilters(array $filtros, array $order)
    {
        return $this->newQuery()
            ->when(!empty($filtros['exercicio']), function (Builder $query) use ($filtros) {
                $query->where('o56_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['elemento']), function (Builder $query) use ($filtros) {
                $query->where('o56_elemento', 'like', "{$filtros['elemento']}%");
            })
            ->when(!empty($filtros['autocomplete']), function (Builder $query) use ($filtros) {
                $string = $filtros['autocomplete'];
                $query->whereRaw("o56_elemento || o56_descr ilike '%$string%'");
            })
            ->when(!empty($filtros['apenasComDotacao']), function (Builder $query) {
                $query->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('orcamento.orcdotacao')
                        ->whereRaw('o58_codele = o56_codele')
                        ->whereRaw('o58_anousu = o56_anousu');
                });
            })
            ->paginate($filtros["rows"]);
    }
}
