<?php

namespace App\Domain\Financeiro\Orcamento\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;

class FonteRecursoRepository extends BaseRepository
{
    protected $modelClass = FonteRecurso::class;

    public function getByFilters(array $filtros)
    {

        return $this->newQuery()
            ->join('orctiporec', 'o15_codigo', 'orctiporec_id')
            ->join('complementofonterecurso', 'o200_sequencial', 'o15_complemento')
            ->where('exercicio', $filtros['exercicio'])
            ->when(!empty($filtros['codigo']), function ($query) use ($filtros) {
                $query->where('o15_codigo', $filtros['codigo']);
            })
            ->when(array_key_exists('classificacao', $filtros), function ($query) use ($filtros) {
                $query->where('classificacaofr_id', $filtros['classificacao']);
            })
            ->when(array_key_exists('siconfi', $filtros), function ($query) use ($filtros) {
                $query->where('codigo_siconfi', 'like', "_{$filtros['siconfi']}");
            })->when(array_key_exists('complemento', $filtros), function ($query) use ($filtros) {
                $query->where('o15_complemento', '=', $filtros['complemento']);
            })
            ->when(array_key_exists('mostrarRegistros', $filtros), function ($query) use ($filtros) {
                $data = date('Y-m-d', session('DB_datausu'));
                if (!empty($filtros['data'])) {
                    $data = $filtros['data'];
                }
                if ($filtros['mostrarRegistros'] === 'ativos') {
                    $query->whereRaw("(o15_datalimite is null or o15_datalimite <= '{$data}')");
                }
                if ($filtros['mostrarRegistros'] === 'inativos') {
                    $query->where("o15_datalimite", ">", $data);
                }
            })
            ->paginate($filtros["rows"]);
    }
}
