<?php

namespace App\Domain\Financeiro\Orcamento\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Orcamento\Models\NaturezaReceita;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class NaturezaReceitaRepositoty extends BaseRepository
{
    protected $modelClass = NaturezaReceita::class;

    public function getByFilters(array $filtros, array $ordens)
    {
        return $this->newQuery()
            ->when(!empty($filtros['exercicio']), function (Builder $query) use ($filtros) {
                $query->where('o57_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['estrutural']), function (Builder $query) use ($filtros) {
                $query->where('o57_fonte', $filtros['estrutural']);
            })
            ->when(!empty($filtros['autocomplete']), function (Builder $query) use ($filtros) {
                $string = $filtros['autocomplete'];
                $query->whereRaw("o57_fonte || o57_descr ilike '%$string%'");
            })
            ->when(!empty($filtros['apenasComReceita']), function (Builder $query) {
                $query->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('orcamento.orcreceita')
                        ->whereRaw('o70_codfon = o57_codfon')
                        ->whereRaw('o70_anousu = o57_anousu');
                });
            })
            ->when(!empty($ordens), function ($query) use ($ordens) {
                foreach ($ordens as $order) {
                    $query->orderBy($order);
                }
            })
            ->paginate($filtros["rows"]);
    }

    public function receitasDecendio(array $filtros, $campos = null)
    {
        if (empty($campos)) {
            $campos = [
                'o70_anousu', 'o70_codrec', 'o70_instit', 'o70_codigo', 'o57_fonte', 'o57_descr',
                'k224_tipodesdobramento', 'codigo_siconfi', 'gestao',  'o15_complemento', 'o15_recurso'
            ];
        }

        $query = $this->newQuery()
            ->select($campos)
            ->join('orcamento.orcreceita', function (JoinClause $join) {
                $join->on('o70_codfon', '=', 'o57_codfon')
                    ->on('o70_anousu', '=', 'o57_anousu');
            })
            ->join('orcamento.orctiporec', 'o15_codigo', 'o70_codigo')
            ->join('orcamento.fonterecurso', function (JoinClause $join) {
                $join->on('orctiporec_id', '=', 'o15_codigo')
                    ->on('exercicio', '=', 'o70_anousu');
            })
            ->leftJoin('caixa.receitadescendio', function (JoinClause $join) {
                $join->on('k224_reduz', '=', 'o70_codrec')
                    ->on('k224_anousu', '=', 'o70_anousu');
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('o70_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['estrutural']), function ($query) use ($filtros) {
                $query->where('o57_fonte', 'like', "{$filtros['estrutural']}%");
            })
            ->when(!empty($filtros['desdobramento']), function ($query) use ($filtros) {
                $str = "(k224_tipodesdobramento is null or k224_tipodesdobramento = {$filtros['desdobramento']})";
                $query->whereRaw($str);
            })
            ->when(empty($filtros['estrutural']), function ($query) use ($filtros) {
                $query->whereRaw("(o57_fonte like '4%' or  o57_fonte like '9%')");
            })
            ->when(!empty($filtros['configurada']), function ($query) use ($filtros) {
                $query->whereRaw('k224_codigo is not null');
            })
            ->orderBy('o57_fonte');

        if (!empty($filtros['rows'])) {
            return $query->paginate($filtros['rows']);
        }
        return $query->get();
    }
}
