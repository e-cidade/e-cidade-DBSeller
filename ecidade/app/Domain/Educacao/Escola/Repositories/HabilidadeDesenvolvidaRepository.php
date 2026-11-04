<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\HabilidadeDesenvolvida;

class HabilidadeDesenvolvidaRepository extends BaseRepository
{
    protected $modelClass = HabilidadeDesenvolvida::class;

    public function getByFilters($filters, $campos = "*")
    {
        $query = $this->newQuery();
        foreach ($filters as $key => $filter) {
            switch ($key) {
                case 'conteudo':
                    $query->where('ed156_diario_classe_bncc', $filter);
                    break;
                case 'disciplina':
                    $query->whereIn('ed156_bnccdisciplinas', $filter);
                    break;
            }
        }

        return $query->select($campos)->get();
    }

    public function salvar($parametros)
    {
        $pk = array_remove($parametros, 'ed156_codigo');
        if (is_null($pk)) {
            return $this->newQuery()->create($parametros);
        } else {
            return tap($this->find($pk))->update($parametros);
        }
    }
    public function deleteByFilters($filtros)
    {
        $query = $this->newQuery();
        foreach ($filtros as $key => $filtro) {
            switch ($key) {
                case 'disciplina':
                    $query->where('ed156_bnccdisciplinas', $filtro);
                    break;
                case 'conteudo':
                    $query->where('ed156_diario_classe_bncc', $filtro);
                    break;
            }
        }

        return $query->delete();
    }
}
