<?php

namespace App\Domain\Financeiro\Orcamento\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Orcamento\Models\Receita;
use Illuminate\Database\Query\JoinClause;

class ReceitaRepository extends BaseRepository
{
    protected $modelClass = Receita::class;

    public function getByFilters(array $filtros, array $ordens = [])
    {
        $campos = [
            'o70_anousu',
            'o70_codrec',
            'o70_valor',
            'o70_codfon', 'o57_fonte', 'o57_descr',
            'o15_codigo', 'o15_descr', 'o15_recurso', 'o15_complemento', 'codigo_siconfi', 'gestao', 'descricao',
            'o70_instit', 'nomeinst',
            'o70_concarpeculiar', 'c58_descr',
            'o70_datacriacao',
            'o70_orcorgao', 'o70_orcunidade', 'o41_descr',
            'o70_esferaorcamentaria'
        ];

        $query = $this->newQuery()
            ->select($campos)
            ->join('orcamento.orcfontes', function (JoinClause $join) {
                $join->on('o57_codfon', 'o70_codfon')->on('o57_anousu', 'o70_anousu');
            })
            ->join('orcamento.orcunidade', function (JoinClause $join) {
                $join->on('o41_anousu', 'o70_anousu')
                    ->on('o41_orgao', 'o70_orcorgao')
                    ->on('o41_unidade', 'o70_orcunidade');
            })
            ->join('orctiporec', 'o15_codigo', '=', 'o70_codigo')
            ->join('complementofonterecurso', 'o200_sequencial', '=', 'o15_complemento')
            ->join('orcamento.fonterecurso', function (JoinClause $join) {
                $join->on('orctiporec_id', 'o15_codigo')->on('exercicio', 'o70_anousu');
            })
            ->join('concarpeculiar', 'c58_sequencial', '=', 'o70_concarpeculiar')
            ->join('db_config', 'codigo', '=', 'o70_instit')
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('o70_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['instituicao']), function ($query) use ($filtros) {
                $query->where('o70_instit', $filtros['instituicao']);
            })
            ->when(!empty($filtros['reduzido']), function ($query) use ($filtros) {
                $query->where('o70_codrec', $filtros['reduzido']);
            })
            ->when(!empty($filtros['codigoNaturezaReceita']), function ($query) use ($filtros) {
                $query->where('o70_codfon', $filtros['codigoNaturezaReceita']);
            })
            ->when(!empty($filtros['estrutural']), function ($query) use ($filtros) {
                $query->where('o57_fonte', 'like', "{$filtros['estrutural']}%");
            })
            ->when(!empty($filtros['codigoRecurso']), function ($query) use ($filtros) {
                $query->where('o70_codigo', $filtros['codigoRecurso']);
            })
            ->when(!empty($ordens), function ($query) use ($ordens) {
                foreach ($ordens as $order) {
                    $query->orderBy($order);
                }
            })->when(empty($ordens) && !empty($filtros['sortField']), function ($query) use ($filtros) {
                $query->orderBy($filtros['sortField'], $filtros['sortOrder']);
            });

        $paginate = !empty($filtros["rows"]);
        $rows = !empty($filtros["rows"]) ? $filtros["rows"] : false;
        return $this->doQuery($query, $rows, $paginate);
    }
}
