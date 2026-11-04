<?php

namespace App\Domain\Financeiro\Contabilidade\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Contabilidade\Models\Historico;
use Illuminate\Support\Facades\DB;

class HistoricoRepository extends BaseRepository
{
    protected $modelClass = Historico::class;
    public function getByFilters(array $filtros, $ordens)
    {
        $campos = [
            "c50_codhist",
            "c50_descr",
            DB::raw("c50_codhist || ' - ' || c50_descr as apresentar")
        ];
        return Historico::query()
            ->select($campos)
            ->when(!empty($filtros['autocomplete']), function ($query) use ($filtros) {
                $string = $filtros['autocomplete'];
                $query->whereRaw("c50_codhist || ' - ' || c50_descr ilike '%{$string}%'");
            })
            ->when(!empty($filtros['codigo']), function ($query) use ($filtros) {
                $query->where('c50_codhist', $filtros['codigo']);
            })
            ->when(!empty($filtros['nome']), function ($query) use ($filtros) {
                $nome = $filtros['nome'];

                $query->where('c50_descr', 'ilike', "{$nome}%");
            })
            ->orderBy('c50_codhist')
            ->paginate($filtros['rows']);
    }
}
