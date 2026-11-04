<?php

namespace App\Domain\Financeiro\Orcamento\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Orcamento\Models\Dotacao;

class DotacaoRepository extends BaseRepository
{
    protected $modelClass = Dotacao::class;

    public function getByFilters(array $filtros, $ordens)
    {
        $campos = [
            'o58_anousu',
            'o58_coddot',
            'o58_orgao', 'o40_descr',
            'o58_unidade', 'o41_descr',
            'o58_funcao', 'o52_descr',
            'o58_subfuncao', 'o53_descr',
            'o58_projativ', 'o55_descr',
            'o58_codigo', 'o15_descr', 'o15_recurso', 'o15_complemento', 'codigo_siconfi', 'gestao', 'descricao',
            'o200_descricao',
            'o58_programa', 'o54_descr',
            'o58_codele', 'o56_elemento', 'o56_descr',
            'o58_valor',
            'o58_instit', 'nomeinst',
            'o58_localizadorgastos',
            'o58_datacriacao',
            'o58_concarpeculiar', 'c58_descr',
            'o58_esferaorcamentaria',
        ];

        return $this->newQuery()
            ->select($campos)
            ->join('orcamento.orcorgao', function ($join) {
                $join->on('o40_anousu', 'o58_anousu')->on('o40_orgao', 'o58_orgao');
            })
            ->join('orcamento.orcunidade', function ($join) {
                $join->on('o41_anousu', 'o58_anousu')
                    ->on('o41_orgao', 'o58_orgao')
                    ->on('o41_unidade', 'o58_unidade');
            })
            ->join('orcamento.orcprograma', function ($join) {
                $join->on('o54_anousu', 'o58_anousu')->on('o54_programa', 'o58_programa');
            })
            ->join('orcamento.orcprojativ', function ($join) {
                $join->on('o55_anousu', 'o58_anousu')->on('o55_projativ', 'o58_projativ');
            })
            ->join('orcamento.orcelemento', function ($join) {
                $join->on('o56_anousu', 'o58_anousu')->on('o56_codele', 'o58_codele');
            })
            ->join('orctiporec', 'o15_codigo', '=', 'o58_codigo')
            ->join('complementofonterecurso', 'o200_sequencial', '=', 'o15_complemento')
            ->join('orcamento.fonterecurso', function ($join) {
                $join->on('orctiporec_id', 'o15_codigo')->on('exercicio', 'o58_anousu');
            })
            ->join('concarpeculiar', 'c58_sequencial', '=', 'o58_concarpeculiar')
            ->join('orcfuncao', 'o52_funcao', '=', 'o58_funcao')
            ->join('db_config', 'codigo', '=', 'o58_instit')
            ->join('orcsubfuncao', 'o53_subfuncao', '=', 'o58_subfuncao')
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('o58_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['instituicao']), function ($query) use ($filtros) {
                $query->where('o58_instit', $filtros['instituicao']);
            })
            ->when(!empty($filtros['elemento']), function ($query) use ($filtros) {
                $query->where('o58_codele', $filtros['elemento']);
            })
            ->when(!empty($filtros['reduzido']), function ($query) use ($filtros) {
                $query->where('o58_coddot', $filtros['reduzido']);
            })
            ->when(!empty($ordens), function ($query) use ($ordens) {
                foreach ($ordens as $order) {
                    $query->orderBy($order);
                }
            })
            ->when(empty($ordens) && !empty($filtros['sortField']), function ($query) use ($filtros) {
                $query->orderBy($filtros['sortField'], $filtros['sortOrder']);
            })
            ->paginate($filtros["rows"]);
    }
}
